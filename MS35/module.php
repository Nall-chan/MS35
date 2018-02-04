<?php

/**
 * @addtogroup ms35
 * @{
 *
 * @package       MS35
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2018 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.03
 */
require_once(__DIR__ . "/MS35Class.php");  // diverse Klassen

/**
 * MS35 ist die Klasse für einen RGB-Controller MS35 von der Fa.Conrad
 * Erweitert ipsmodule
 *
 * @property string $Buffer Receive Buffer.
 * @property bool $Connected Aktuell verbunden ?
 * @property bool $SetReplyEvent Daten empfangen.
 * @property int $ParentId Aktueller IO-Parent.
 */
class MS35 extends IPSModule
{
    use VariableHelper,
        DebugHelper,
        Semaphore,
        VariableProfile,
        InstanceStatus,
        BufferHelper
    {
        InstanceStatus::MessageSink as IOMessageSink; // MessageSink gibt es sowohl hier in der Klasse, als auch im Trait InstanceStatus. Hier wird für die Methode im Trait ein Alias benannt.
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Destroy()
    {
        parent::Destroy();

        $this->UnregisterProfil("MS35.Program");
        $this->UnregisterProfil("MS35.PrgStatus");
        $this->UnregisterProfil("MS35.Speed");
        $this->UnregisterProfil("MS35.Brightness");
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ApplyChanges()
    {
        $this->RegisterMessage(0, IPS_KERNELSTARTED);
        $this->RegisterMessage($this->InstanceID, FM_CONNECT);
        $this->RegisterMessage($this->InstanceID, FM_DISCONNECT);

        parent::ApplyChanges();

        $this->RegisterProfileIntegerEx("MS35.Program", "Gear", "", "", array(
            array(1, 'Farbwechsel 1', '', -1),
            array(2, 'Farbwechsel 2', '', -1),
            array(3, 'Farbwechsel 3', '', -1),
            array(4, 'Gewitter', '', -1),
            array(5, 'Kaminfeuer', '', -1),
            array(6, 'Sonnenauf- & untergang', '', -1),
            array(7, 'Farbblitze', '', -1),
            array(8, 'User 1', '', -1),
            array(9, 'User 2', '', -1)
        ));

        $this->RegisterProfileIntegerEx("MS35.PrgStatus", "Bulb", "", "", array(
            array(1, 'Play', '', -1),
            array(2, 'Pause', '', -1),
            array(3, 'Stop', '', -1)
        ));

        $this->RegisterProfileIntegerEx("MS35.Speed", "Intensity", "", "", array(
            array(0, 'normal', '', -1),
            array(1, '1/2', '', -1),
            array(2, '1/4', '', -1),
            array(3, '1/8', '', -1),
            array(4, '1/16', '', -1),
            array(5, '1/32', '', -1),
            array(6, '1/64', '', -1),
            array(7, '1/128', '', -1)
        ));

        $this->RegisterProfileIntegerEx("MS35.Brightness", "Sun", "", "", array(
            array(1, 'normal', '', -1),
            array(2, '1/2', '', -1),
            array(3, '1/3', '', -1)
        ));

        $this->RegisterVariableBoolean("STATE", "STATE", "~Switch", 1);
        $this->EnableAction("STATE");
        $this->RegisterVariableInteger("Color", "Color", "~HexColor", 2);
        $this->EnableAction("Color");
        $this->RegisterVariableInteger("Program", "Program", "MS35.Program", 3);
        $this->EnableAction("Program");
        $this->RegisterVariableInteger("Play", "Play", "MS35.PrgStatus", 4);
        $this->EnableAction("Play");
        $this->RegisterVariableInteger("Speed", "Speed", "MS35.Speed", 5);
        $this->EnableAction("Speed");
        $this->RegisterVariableInteger("Brightness", "Brightness", "MS35.Brightness", 6);
        $this->EnableAction("Brightness");

        // Remove OLD Workaround
        $this->UnregisterVariable("BufferIN");
        $this->UnregisterVariable("ReplyEvent");
        $this->UnregisterVariable("Connected");

        // Wenn Kernel nicht bereit, dann warten... KR_READY kommt ja gleich
        if (IPS_GetKernelRunlevel() <> KR_READY) {
            return;
        }

        // Wenn Parent aktiv, dann Anmeldung an der Hardware bzw. Datenabgleich starten
        if ($this->HasActiveParent()) {
            $this->IOChangeState(IS_ACTIVE);
        } else {
            $this->SetStatus(IS_INACTIVE);
        }
    }

    /**
     * Nachrichten aus der Nachrichtenschlange verarbeiten.
     *
     * @access public
     * @param int $TimeStamp
     * @param int $SenderID
     * @param int $Message
     * @param array|int $Data
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        switch ($Message) {
            case IPS_KERNELSTARTED:
                $this->RegisterParent();
                if ($this->HasActiveParent()) {
                    $this->IOChangeState(IS_ACTIVE);
                } else {
                    $this->IOChangeState(IS_INACTIVE);
                }
                break;
        }
    }

    /**
     * Wird ausgeführt wenn sich der Status vom Parent ändert.
     * @access protected
     */
    protected function IOChangeState($State)
    {
        // Anzeige Port in der INFO Spalte
        if ($this->ParentId > 0) {
            $ParentInstance = IPS_GetInstance($this->ParentId);
            if ($ParentInstance['ModuleInfo']['ModuleID'] == '{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}') {
                $this->SetSummary(IPS_GetProperty($this->ParentId, 'Port'));
            } else {
                $config = json_decode(IPS_GetConfiguration($this->ParentId), true);
                if (array_key_exists('Port', $config)) {
                    $this->SetSummary($config['Port']);
                } elseif (array_key_exists('Host', $config)) {
                    $this->SetSummary($config['Host']);
                } elseif (array_key_exists('Address', $config)) {
                    $this->SetSummary($config['Address']);
                } elseif (array_key_exists('Name', $config)) {
                    $this->SetSummary($config['Name']);
                }
                $this->SetSummary('see ' . $this->ParentId);
            }
        } else {
            $this->SetSummary('(none)');
        }

        // Wenn der IO Aktiv wurde
        if ($State == IS_ACTIVE) {
            $this->DoInit();
        } else { // und wenn nicht
            $this->SetStatus(IS_INACTIVE);
        }
    }

    public function GetConfigurationForParent()
    {
        $ParentInstance = IPS_GetInstance($this->ParentId);
        if ($ParentInstance['ModuleInfo']['ModuleID'] == '{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}') {
            $Config['StopBits'] = 1;
            $Config['BaudRate'] = 38400;
            $Config['Parity'] = 'None';
            $Config['DataBits'] = 8;
            return json_encode($Config);
        } else { // Kein SerialPort, sondern TCP oder XBEE Brücke. User muss selber den Port am Endgerät einstellen.
            return json_encode(array());
        }
    }

    ################## PUBLIC

    /**
     * IPS-Instanz Funktion MS35_SendSwitch.
     * Schaltet den Controller ein oder aus.
     *
     * @access public
     * @param bool $State true für ein, false für aus.
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SendSwitch(bool $State)
    {
        if ($State) { //Einschalten
            if (!$this->Connected) {
                try {
                    $this->DoInit();
                } catch (Exception $exc) {
                    trigger_error($exc->getMessage(), $exc->getCode());
                    return false;
                }
            }
            $this->SetValueBoolean('STATE', true);
            return true;
        } else { //Ausschalten
            $data = chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);  // farbe weg
            if ($this->SendCommand($data)) {
                $this->SetValueBoolean('STATE', false);
                $this->SetValueInteger('Color', 0);
                $this->SetValueInteger('Play', 3);
                $data = chr(0x0B) . chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
                if ($this->SendCommand($data)) {
                    $this->SetValueInteger('Speed', 0);
                }
                $data = chr(0x0C) . chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
                if ($this->SendCommand($data)) {
                    $this->SetValueInteger('Brightness', 1);
                }
                $data = chr(0x01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
                $this->SendCommand($data);
                return true;
            }
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_SetRGB.
     * Setzt eine Farbe.
     *
     * @access public
     * @param int $Red Anteil Rot von 0 bis 255.
     * @param int $Green Anteil Grün von 0 bis 255.
     * @param int $Blue Anteil Blau von 0 bis 255.
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SetRGB(int $Red, int $Green, int $Blue)
    {
        if (($Red < 0) or ($Red > 255) or ($Green < 0) or ($Green > 255) or ($Blue < 0) or ($Blue > 255)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }
        $Data = chr(01) . chr(00) . chr($Red) . chr($Green) . chr($Blue) . chr(00) . chr(00);
        $Color = ($Red << 16) + ($Green << 8) + $Blue;
        if ($this->SendCommand($Data)) {
            $this->SetValueInteger('Color', $Color);
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Play', 3);
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_Play.
     * Startet die Wiedergabe des aktiven Programms bzw. setzt ein pausiertes fort.
     *
     * @access public
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function Play()
    {
        $Data = chr(0x0A) . chr(0x07) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Play'
        if ($this->SendCommand($Data)) {
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Play', 1);
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_Pause.
     * Pausiert die aktuelle Wiedergabe des aktiven Programmes.
     *
     * @access public
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function Pause()
    {
        $Data = chr(0x0A) . chr(0x06) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Pause'
        if ($this->SendCommand($Data)) {
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Play', 2);
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_Stop.
     * Stopt die Wiedergabe des aktiven Programmes.
     *
     * @access public
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function Stop()
    {
        $Color = GetValueInteger($this->GetIDForIdent('Color'));
        $Red = ($Color & 0x00ff0000) >> 16;
        $Green = ($Color & 0x0000ff00) >> 8;
        $Blue = $Color & 0x000000ff;
        $Data = chr(01) . chr(00) . chr($Red) . chr($Green) . chr($Blue) . chr(00) . chr(00);
        if ($this->SendCommand($Data)) {
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Play', 3); //stop
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_RunProgram.
     * Startet die Wiedergabe eines Programmes.
     *
     * @access public
     * @param int $Programm Die Nummer des zu startenden Programmes (1-9).
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function RunProgram(int $Programm)
    {
        if (($Programm < 1) or ($Programm > 9)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }

        $data = array();
        $data[0] = chr(0x0A) . chr(0x01) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Color3'
        $data[1] = chr(0x0A) . chr(0x01) . chr(0x02) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Color2'
        $data[2] = chr(0x0A) . chr(0x01) . chr(0x03) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Color1'
        $data[3] = chr(0x0A) . chr(0x01) . chr(0x04) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Gewitter'    ohne Speed
        $data[4] = chr(0x0A) . chr(0x01) . chr(0x05) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Fire'        ohne Speed
        $data[5] = chr(0x0A) . chr(0x01) . chr(0x06) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Sunrise'
        $data[6] = chr(0x0A) . chr(0x01) . chr(0x07) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Flash'
        $data[7] = chr(0x0A) . chr(0x01) . chr(0x08) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'User1'
        $data[8] = chr(0x0A) . chr(0x01) . chr(0x09) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'User2'
        if ($this->SendCommand($data[$Programm - 1])) {
            $ret = true;
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Program', $Programm);
            $this->SetValueInteger('Play', 1); //play
            $wait = true;
            if (($Programm == 4) or ($Programm == 5)) {
                $this->SetValueInteger('Speed', 0);
            } else {
                $Speed = GetValueInteger($this->GetIDForIdent('Speed'));
                if (($Speed < 0) or ($Speed > 8)) {
                    $this->SetValueInteger('Speed', 0);
                } else {
                    if ($Speed <> 0) {
                        $send = chr(0x0B) . chr(intval(pow(2, $Speed))) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
                        IPS_Sleep(400);
                        $wait = false;
                        if ($this->SendCommand($send) === false) {
                            $ret = false;
                        }
                    }
                }
            }
            $Brightness = GetValueInteger($this->GetIDForIdent('Brightness'));
            if (($Brightness < 1) or ($Brightness > 3)) {
                $this->SetValueInteger('Brightness', 1);
            } else {
                if ($Brightness <> 1) {
                    $send = chr(0x0C) . chr(value) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
                    if ($wait) {
                        IPS_Sleep(400);
                    }
                    if ($this->SendCommand($send) === false) {
                        $ret = false;
                    }
                }
            }
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * IPS-Instanz Funktion MS35_SetSpeed.
     * Setzt die Geschwindigkeit der Wiedergabe des aktiven Programmes.
     *
     * @access public
     * @param int $Speed Die Geschwindikeit von 0-8 einer Verlangsamung mit den Faktoren 1,2,4,8,16,32,64,128 entspricht.
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SetSpeed(int $Speed)
    {
        if (($Speed < 0) or ($Speed > 8)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }
        $Program = GetValueInteger($this->GetIDForIdent('Program'));
        if (($Program <> 4) and ($Program <> 5)) {
            $data = chr(0x0B) . chr(intval(pow(2, $Speed))) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
            if ($this->SendCommand($data)) {
                $this->SetValueInteger('Speed', $Speed);
                return true;
            }
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_SetBrightness.
     * Setzt die Helligkeit.
     *
     * @access public
     * @param int $Level Helligkeit  1=normal, 2 = mittel, 3 = dunkel.
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SetBrightness(int $Level)
    {
        if (($Level < 1) or ($Level > 3)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }
        $data = chr(0x0C) . chr($Level) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
        if ($this->SendCommand($data)) {
            $this->SetValueInteger('Brightness', $Level);
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_SetProgram.
     * Speicher ein Programm im Controller.
     *
     * @access public
     * @param int $Programm Zu beschreibendes Programm (8 oder 9).
     * @param string $Data JSON-String mit dem Programm.
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SetProgram(int $Programm, string $Data)
    {
        if (($Programm < 8) or ($Programm > 9)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }

        $PrgData = json_decode($Data);
        if ($PrgData == null) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }

        if ($Programm == 8) {
            $Programm = 2;
        }
        if ($Programm == 9) {
            $Programm = 4;
        }

        $i = count($PrgData);
        if (($i < 1) or ($i > 51)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }

        if ($this->SendCommand(chr($Programm) . chr($i) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0))) {
            $ret = true;
            $Programm++;

            foreach ($PrgData as $i => $Slot) {
                $Red = $Slot->R;
                $Green = $Slot->G;
                $Blue = $Slot->B;
                $Fade = $Slot->F;
                $Hold = $Slot->H;
                if (($Red < 0) or ($Red > 255) or ($Green < 0) or ($Green > 255) or ($Blue < 0) or ($Blue > 255) or ($Fade < 0) or ($Fade > 255) or ($Hold < 0) or ($Hold > 255)) {
                    trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
                    $ret = false;
                    continue;
                }
                if ($this->SendCommand(chr($Programm) . chr($i + 1) . chr($Red) . chr($Green) . chr($Blue) . chr($Fade) . chr($Hold)) === false) {
                    $ret = false;
                }
            }
            return $ret;
        }
        return false;
    }

    ################## ActionHandler

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
            case 'STATE':
                $this->SendSwitch($Value); //SendInit();
                break;
            case 'Color':
                $r = ($Value & 0x00ff0000) >> 16;
                $g = ($Value & 0x0000ff00) >> 8;
                $b = $Value & 0x000000ff;
                $this->SetRGB($r, $g, $b);

                break;
            case 'Program':
                $this->RunProgram($Value);
                break;
            case 'Play':
                switch ($Value) {
                    case 1:
                        $this->Play();
                        break;
                    case 2:
                        $this->Pause();
                        break;
                    case 3:
                        $this->Stop();
                        break;
                    default:
                        trigger_error($this->Translate('Invalid value'), E_USER_NOTICE);
                        break;
                }
                break;
            case 'Speed':
                $this->SetSpeed($Value);
                break;
            case 'Brightness':
                $this->SetBrightness($Value);
                break;
            default:
                trigger_error($this->Translate('Invalid ident'), E_USER_NOTICE);
                break;
        }
    }

    ################## PRIVATE

    /**
     * Sendet ein Command an den Controller.
     *
     * @access private
     * @param string $Data Der Binäre Command-String
     * @return boolean True bei erfolg, sonst false.
     */
    private function SendCommand(string $Data)
    {
        if ($this->InitRun) {
            return false;
        }

        if (!$this->Connected) {
            try {
                if (!$this->SendInit()) {
                    return false;
                }
            } catch (Exception $exc) {
                trigger_error($exc->getMessage(), E_USER_NOTICE);
                return false;
            }
        }

        if ($this->lock('SendCommand')) {
            try {
                $this->SendDebug('Send', $Data, 1);
                $sendok = $this->SendDataToParent($this->AddCRC16($Data));
            } catch (Exception $exc) {
                $this->unlock('SendCommand');
                trigger_error($exc->getMessage(), $exc->getCode());
                return false;
            }
            if ($sendok) {
                if ($this->WaitForResponse(1000)) {    //warte auf Reply
                    $Buffer = $this->Buffer;
                    $this->Buffer = '';
                    if ($Buffer == 'a') {
                        $this->SendDebug('ACK', '', 1);

                        $this->unlock('SendCommand');
                        return true;
                    } else {
                        $this->SendDebug('NACK', '', 1);
                        $this->Connected = false;
                        $this->unlock('SendCommand');
                        trigger_error($this->Translate('Controller send NACK.'), E_USER_NOTICE);
                        return false;
                    }
                } else {
                    //Senddata('Error','Timeout');
                    $this->SendDebug('Timeout', '', 1);

                    $this->Connected = false;
                    $this->unlock('SendCommand');
                    trigger_error($this->Translate('Controller do not response.'), E_USER_NOTICE);
                    return false;
                }
            } else {
                $this->SendDebug('Timeout', '', 1);
                $this->unlock('SendCommand');
                trigger_error($this->Translate('Controller do not response.'), E_USER_NOTICE);
                return false;
            }
        } else {
            $this->SendDebug('Timeout', '', 1);

            trigger_error($this->Translate('Send is blocked.'), E_USER_NOTICE);
            return false;
        }
    }

    /**
     * Initialisiert den Controller und setzt die Statusvariablen auf einen definierten Wert.
     *
     * @access private
     * @return boolean True bei Erflog, sonst false.
     */
    private function DoInit()
    {
        $this->SendDebug('Start Init', 'Instance', 0);

        try {
            $ret = $this->SendInit();
        } catch (Exception $exc) {
            trigger_error($exc->getMessage(), E_USER_NOTICE);
            return false;
        }
        if (!$ret) {
            return false;
        }


        $this->SetValueBoolean('STATE', true);
        $data = chr(0x0B) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data)) {
            $this->SetValueInteger('Speed', 0);
        }
        $data = chr(0x0C) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data)) {
            $this->SetValueInteger('Brightness', 1);
        }
        $data = chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data)) {
            $this->SetValueInteger('Color', 0);
        }
        $this->SetValueInteger('Play', 3);
        $this->SetValueInteger('Program', 1);
        $this->SendDebug('End Init', 'Instance', 0);

        return true;
    }

    /**
     * Sendet die Initialisierung an den Controller und prüft die Rückmeldung.
     *
     * @access private
     * @return boolean True bei Erfolg, sonst false.
     * @throws Exception Wenn kein aktiver Parent verbunden ist.
     */
    private function SendInit()
    {
        if ($this->InitRun) {
            return false;
        }
        $this->SendDebug('Start Init', 'Controller', 0);

        $this->InitRun = true;
        $InitState = false;
        for ($i = 0; $i < 9; $i++) {
            try {
                $this->SendDebug('Send', chr(0xFD), 1);
                $sendok = $this->SendDataToParent(chr(0xFD));
            } catch (Exception $exc) {
                $this->InitRun = false;
                $this->Connected = false;
                throw $exc;
            }
            if ($sendok) {
                if ($this->WaitForResponse(250)) {    //warte auf Reply
                    $Buffer = $this->Buffer;
                    $this->Buffer = '';
                    if ($Buffer == 'e') {
                        $this->SendDebug('Receive Sync', '', 1);
                        $InitState = true;
                        $i = 9;
                    }
                }
            } else {
                $i = 9;
            }
        }
        if ($InitState) {
            $InitState = false;
            try {
                $this->SendDebug('Send', chr(0xFD) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0xCF) . chr(0x2C), 1);
                $sendok = $this->SendDataToParent(chr(0xFD) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0xCF) . chr(0x2C));
            } catch (Exception $exc) {
                $this->InitRun = false;
                $this->Connected = false;
                throw $exc;
            }

            if ($sendok) {
                for ($i = 0; $i < 8; $i++) {
                    if ($this->WaitForResponse(250)) {    //warte auf Reply
                        $Buffer = $this->Buffer;
                        if (strpos($Buffer, 'C_RGB')) {
                            $this->Buffer = '';
                            $this->SendDebug('Controller ident', $Buffer, 0);
                            $InitState = true;
                            $i = 4;
                        }
                    }
                }
            }
        }

        if ($InitState) {
            $this->Connected = true;
            $this->InitRun = false;
            $this->SendDebug('End Init', 'Controller', 0);
            return true;
        }
        $this->Connected = false;
        $this->InitRun = false;
        $this->SendDebug('Error Init', 'Controller', 0);
        throw new Exception($this->Translate('Could not initialize controller.'), E_USER_NOTICE);
    }

    /**
     * Fügt dem übergebenden String eine CRC16 hinzu.
     *
     * @access private
     * @param string $string String aus welchem die CRC gebildet wird.
     * @return string Der übergebene String mit angehängter CRC16 Checksumme.
     */
    private function AddCRC16(string $string)
    {
        $crc = 0;
        for ($x = 0; $x < strlen($string); $x++) {
            $crc = $crc ^ ord($string[$x]);
            for ($y = 0; $y < 8; $y++) {
                if (($crc & 0x0001) == 0x0001) {
                    $crc = (($crc >> 1) ^ 0xA001);
                } else {
                    $crc = $crc >> 1;
                }
            }
        }
        $high_byte = ($crc & 0xff00) / 256;
        $low_byte = $crc & 0x00ff;

        $string = $string . chr($high_byte) . chr($low_byte);
        return $string;
    }

    /**
     * Warte auf das SetReply Event.
     *
     * @access private
     * @param int $Timeout Max. Zeit in ms in der dass Event eintreffen muss.
     * @return boolean True wenn das Event eintrifft, false wenn Timeout erreicht wurde.
     */
    private function WaitForResponse(int $Timeout)
    {
        for ($i = 0; $i < $Timeout / 5; $i++) {
            if ($this->SetReplyEvent) {
                $this->SetReplyEvent = false;
                return true;
            } else {
                IPS_Sleep(5);
            }
        }
        return false;
    }

    ################## DATAPOINTS

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);

        // Stream zusammenfügen
        $this->Buffer .= utf8_decode($data->Buffer);
        // Empfangs Event setzen
        $this->SetReplyEvent = true;
        return true;
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access protected
     */
    protected function SendDataToParent($Data)
    {
        if (!$this->HasActiveParent()) {
            throw new Exception($this->Translate("Instance has no active parent."), E_USER_NOTICE);
        }
        $result = parent::SendDataToParent(json_encode(array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Data))));
        return ($result === false ? false : true);
    }
}

/** @} */
