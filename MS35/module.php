<?php

declare(strict_types=1);
/*
 * @addtogroup ms35
 * @{
 *
 * @package       MS35
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2025 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.22
 */
eval('declare(strict_types=1);namespace MS35 {?>' . file_get_contents(__DIR__ . '/../libs/helper/BufferHelper.php') . '}');
eval('declare(strict_types=1);namespace MS35 {?>' . file_get_contents(__DIR__ . '/../libs/helper/ParentIOHelper.php') . '}');
eval('declare(strict_types=1);namespace MS35 {?>' . file_get_contents(__DIR__ . '/../libs/helper/SemaphoreHelper.php') . '}');
eval('declare(strict_types=1);namespace MS35 {?>' . file_get_contents(__DIR__ . '/../libs/helper/DebugHelper.php') . '}');
eval('declare(strict_types=1);namespace MS35 {?>' . file_get_contents(__DIR__ . '/../libs/helper/VariableProfileHelper.php') . '}');

/**
 * MS35 ist die Klasse für einen RGB-Controller MS35 von der Fa.Conrad
 * Erweitert IPSModuleStrict
 *
 * @property string $Buffer Receive Buffer
 * @property bool $InitRun Init läuft
 * @property bool $Connected Aktuell verbunden
 * @property bool $SetReplyEvent Daten empfangen
 * @property int $ParentID Aktueller IO-Parent
 * @method void UnregisterProfile(string $Name)
 * @method bool lock(string $ident)
 * @method void unlock(string $ident)
 * @method bool IORequestAction(string $Ident, mixed $Value)
 * @method void IOMessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data)
 * @method int IORegisterParent()
 */
class MS35 extends IPSModuleStrict
{
    use \MS35\DebugHelper,
        \MS35\Semaphore,
        \MS35\VariableProfileHelper,
        \MS35\InstanceStatus,
        \MS35\BufferHelper {
            \MS35\InstanceStatus::MessageSink as IOMessageSink;
            \MS35\InstanceStatus::RegisterParent as IORegisterParent;
            \MS35\InstanceStatus::RequestAction as IORequestAction;
        }
    private const PRESENTATION = 'PRESENTATION';

    /**
     * Create
     *
     * @return void
     */
    public function Create(): void
    {
        parent::Create();
        $this->Buffer = '';
        $this->InitRun = false;
        $this->Connected = false;
        $this->SetReplyEvent = false;
    }

    /**
     * ApplyChanges
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        $this->RegisterMessage(0, IPS_KERNELSTARTED);
        $this->RegisterMessage($this->InstanceID, FM_CONNECT);
        $this->RegisterMessage($this->InstanceID, FM_DISCONNECT);

        parent::ApplyChanges();

        $this->UnregisterProfile('MS35.Program');
        $this->UnregisterProfile('MS35.PrgStatus');
        $this->UnregisterProfile('MS35.Speed');
        $this->UnregisterProfile('MS35.Brightness');

        $this->RegisterVariableBoolean(
            'STATE',
            $this->Translate('State'),
            [
                self::PRESENTATION => VARIABLE_PRESENTATION_SWITCH
            ],
            1
        );
        $this->EnableAction('STATE');
        $this->RegisterVariableInteger(
            'Color',
            $this->Translate('Color'),
            [
                self::PRESENTATION      => VARIABLE_PRESENTATION_COLOR
            ],
            2
        );
        $this->EnableAction('Color');
        $this->RegisterVariableInteger(
            'Program',
            $this->Translate('Program'),
            [
                self::PRESENTATION => VARIABLE_PRESENTATION_ENUMERATION,
                'ICON'             => 'Gear',
                'LAYOUT'           => 0,
                'OPTIONS'          => json_encode(
                    [
                        [
                            'Value'      => 1,
                            'Caption'    => $this->Translate('Color change 1'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 2,
                            'Caption'    => $this->Translate('Color change 2'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 3,
                            'Caption'    => $this->Translate('Color change 3'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 4,
                            'Caption'    => $this->Translate('Thunderstorm'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 5,
                            'Caption'    => $this->Translate('Fire'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 6,
                            'Caption'    => $this->Translate('Sunrise and sunset'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 7,
                            'Caption'    => $this->Translate('Flashes of color'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 8,
                            'Caption'    => $this->Translate('User 1'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 9,
                            'Caption'    => $this->Translate('User 2'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ]
                    ]
                )
            ],
            3
        );
        $this->EnableAction('Program');
        $this->RegisterVariableInteger(
            'Play',
            $this->Translate('Play'),
            [
                self::PRESENTATION => VARIABLE_PRESENTATION_ENUMERATION,
                'ICON'             => 'Bulb',
                'LAYOUT'           => 0,
                'OPTIONS'          => json_encode(
                    [
                        [
                            'Value'      => 1,
                            'Caption'    => $this->Translate('Play'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 2,
                            'Caption'    => $this->Translate('Pause'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 3,
                            'Caption'    => $this->Translate('Stop'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ]
                    ]
                )
            ],
            4
        );
        $this->EnableAction('Play');
        $this->RegisterVariableInteger(
            'Speed',
            $this->Translate('Speed'),
            [
                self::PRESENTATION => VARIABLE_PRESENTATION_ENUMERATION,
                'ICON'             => 'Intensity',
                'LAYOUT'           => 0,
                'OPTIONS'          => json_encode(
                    [
                        [
                            'Value'      => 0,
                            'Caption'    => $this->Translate('normal'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 1,
                            'Caption'    => $this->Translate('1/2'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 2,
                            'Caption'    => $this->Translate('1/4'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 3,
                            'Caption'    => $this->Translate('1/8'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 4,
                            'Caption'    => $this->Translate('1/16'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 5,
                            'Caption'    => $this->Translate('1/32'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 6,
                            'Caption'    => $this->Translate('1/64'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 7,
                            'Caption'    => $this->Translate('1/128'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ]
                    ]
                )
            ],
            5
        );
        $this->EnableAction('Speed');
        $this->RegisterVariableInteger(
            'Brightness',
            $this->Translate('Brightness'),
            [
                self::PRESENTATION => VARIABLE_PRESENTATION_ENUMERATION,
                'ICON'             => 'Sun',
                'LAYOUT'           => 0,
                'OPTIONS'          => json_encode(
                    [
                        [
                            'Value'      => 1,
                            'Caption'    => $this->Translate('normal'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 2,
                            'Caption'    => $this->Translate('1/2'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ],
                        [
                            'Value'      => 3,
                            'Caption'    => $this->Translate('1/3'),
                            'IconActive' => false,
                            'IconValue'  => '',
                            'Color'      => -1,
                        ]
                    ]
                )
            ],
            6
        );
        $this->EnableAction('Brightness');

        // Wenn Kernel nicht bereit, dann warten... KR_READY kommt ja gleich
        if (IPS_GetKernelRunlevel() != KR_READY) {
            return;
        }
        $this->RegisterParent();

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
     * @param int       $TimeStamp
     * @param int       $SenderID
     * @param int       $Message
     * @param array|int $Data
     */
    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        $this->IOMessageSink($TimeStamp, $SenderID, $Message, $Data);
        switch ($Message) {
            case IPS_KERNELSTARTED:
                $this->KernelReady();
                break;
        }
    }

    /**
     * GetConfigurationForParent
     *
     * @return string
     */
    public function GetConfigurationForParent(): string
    {
        $ParentInstance = IPS_GetInstance($this->ParentID);
        if ($ParentInstance['ModuleInfo']['ModuleID'] == '{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}') {
            $Config['StopBits'] = '1';
            $Config['BaudRate'] = '38400';
            $Config['Parity'] = 'None';
            $Config['DataBits'] = '8';
            return json_encode($Config);
        } else { // Kein SerialPort
            return json_encode([]);
        }
    }

    //################# PUBLIC

    /**
     * IPS-Instanz Funktion MS35_SendSwitch.
     * Schaltet den Controller ein oder aus.
     *
     * @param bool $State true für ein, false für aus.
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SendSwitch(bool $State): bool
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
            $this->SetValue('STATE', true);
            return true;
        } else { //Ausschalten
            $data = chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);  // farbe weg
            if ($this->SendCommand($data)) {
                $this->SetValue('STATE', false);
                $this->SetValue('Color', 0);
                $this->SetValue('Play', 3);
                $data = chr(0x0B) . chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
                if ($this->SendCommand($data)) {
                    $this->SetValue('Speed', 0);
                }
                $data = chr(0x0C) . chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
                if ($this->SendCommand($data)) {
                    $this->SetValue('Brightness', 1);
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
     * @param int $Red   Anteil Rot von 0 bis 255.
     * @param int $Green Anteil Grün von 0 bis 255.
     * @param int $Blue  Anteil Blau von 0 bis 255.
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SetRGB(int $Red, int $Green, int $Blue): bool
    {
        if (($Red < 0) || ($Red > 255) || ($Green < 0) || ($Green > 255) || ($Blue < 0) || ($Blue > 255)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }
        $Data = chr(01) . chr(00) . chr($Red) . chr($Green) . chr($Blue) . chr(00) . chr(00);
        $Color = ($Red << 16) + ($Green << 8) + $Blue;
        if ($this->SendCommand($Data)) {
            $this->SetValue('Color', $Color);
            $this->SetValue('STATE', true);
            $this->SetValue('Play', 3);
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_Play.
     * Startet die Wiedergabe des aktiven Programms bzw. setzt ein pausiertes fort.
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function Play(): bool
    {
        $Data = chr(0x0A) . chr(0x07) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Play'
        if ($this->SendCommand($Data)) {
            $this->SetValue('STATE', true);
            $this->SetValue('Play', 1);
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_Pause.
     * Pausiert die aktuelle Wiedergabe des aktiven Programmes.
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function Pause(): bool
    {
        $Data = chr(0x0A) . chr(0x06) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Pause'
        if ($this->SendCommand($Data)) {
            $this->SetValue('STATE', true);
            $this->SetValue('Play', 2);
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_Stop.
     * Stopt die Wiedergabe des aktiven Programmes.
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function Stop(): bool
    {
        $Color = GetValueInteger($this->GetIDForIdent('Color'));
        $Red = ($Color & 0x00ff0000) >> 16;
        $Green = ($Color & 0x0000ff00) >> 8;
        $Blue = $Color & 0x000000ff;
        $Data = chr(01) . chr(00) . chr($Red) . chr($Green) . chr($Blue) . chr(00) . chr(00);
        if ($this->SendCommand($Data)) {
            $this->SetValue('STATE', true);
            $this->SetValue('Play', 3); //stop
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_RunProgram.
     * Startet die Wiedergabe eines Programmes.
     *
     * @param int $Programm Die Nummer des zu startenden Programmes (1-9).
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function RunProgram(int $Programm): bool
    {
        if (($Programm < 1) || ($Programm > 9)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }

        $data = [];
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
            $this->SetValue('STATE', true);
            $this->SetValue('Program', $Programm);
            $this->SetValue('Play', 1); //play
            $wait = true;
            if (($Programm == 4) || ($Programm == 5)) {
                $this->SetValue('Speed', 0);
            } else {
                $Speed = GetValueInteger($this->GetIDForIdent('Speed'));
                if (($Speed < 0) || ($Speed > 8)) {
                    $this->SetValue('Speed', 0);
                } else {
                    if ($Speed != 0) {
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
            if (($Brightness < 1) || ($Brightness > 3)) {
                $this->SetValue('Brightness', 1);
            } else {
                if ($Brightness != 1) {
                    $send = chr(0x0C) . chr($Brightness) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
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
     * @param int $Speed Die Geschwindigkeit von 0-8 einer Verlangsamung mit den Faktoren 1,2,4,8,16,32,64,128 entspricht.
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SetSpeed(int $Speed): bool
    {
        if (($Speed < 0) || ($Speed > 8)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }
        $Program = GetValueInteger($this->GetIDForIdent('Program'));
        if (($Program != 4) && ($Program != 5)) {
            $data = chr(0x0B) . chr(intval(pow(2, $Speed))) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
            if ($this->SendCommand($data)) {
                $this->SetValue('Speed', $Speed);
                return true;
            }
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_SetBrightness.
     * Setzt die Helligkeit.
     *
     * @param int $Level Helligkeit  1=normal, 2 = mittel, 3 = dunkel.
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SetBrightness(int $Level): bool
    {
        if (($Level < 1) || ($Level > 3)) {
            trigger_error($this->Translate('Invalid parameter'), E_USER_NOTICE);
            return false;
        }
        $data = chr(0x0C) . chr($Level) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
        if ($this->SendCommand($data)) {
            $this->SetValue('Brightness', $Level);
            return true;
        }
        return false;
    }

    /**
     * IPS-Instanz Funktion MS35_SetProgram.
     * Speicher ein Programm im Controller.
     *
     * @param int    $Programm Zu beschreibendes Programm (8 oder 9).
     * @param string $Data     JSON-String mit dem Programm.
     *
     * @return bool True wenn Befehl erfolgreich ausgeführt wurde, sonst false.
     */
    public function SetProgram(int $Programm, string $Data): bool
    {
        if (($Programm < 8) || ($Programm > 9)) {
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
        if (($i < 1) || ($i > 51)) {
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
                if (($Red < 0) || ($Red > 255) || ($Green < 0) || ($Green > 255) || ($Blue < 0) || ($Blue > 255) || ($Fade < 0) || ($Fade > 255) || ($Hold < 0) || ($Hold > 255)) {
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

    //################# ActionHandler

    /**
     * RequestAction
     *
     * @param  string $Ident
     * @param  mixed $Value
     * @return void
     */
    public function RequestAction(string $Ident, mixed $Value): void
    {
        if ($this->IORequestAction($Ident, $Value)) {
            return;
        }
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

    //################# DATAPOINTS

    /**
     * ReceiveData
     *
     * @param  string $JSONString
     * @return string
     */
    public function ReceiveData(string $JSONString): string
    {
        $data = json_decode($JSONString);

        // Stream zusammenfügen
        $this->Buffer .= hex2bin($data->Buffer);
        // Empfangs Event setzen
        $this->SetReplyEvent = true;
        return '';
    }

    /**
     * KernelReady
     *
     * Wird ausgeführt wenn der Kernel hochgefahren wurde.
     *
     * @return void
     */
    protected function KernelReady(): void
    {
        $this->RegisterParent();
        if ($this->HasActiveParent()) {
            $this->IOChangeState(IS_ACTIVE);
        } else {
            $this->IOChangeState(IS_INACTIVE);
        }
    }

    /**
     * RegisterParent
     *
     * @return void
     */
    protected function RegisterParent(): void
    {
        $IOId = $this->IORegisterParent();
        // Anzeige Port in der INFO Spalte
        if ($IOId > 0) {
            $ParentInstance = IPS_GetInstance($IOId);
            if ($ParentInstance['ModuleInfo']['ModuleID'] == '{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}') {
                $this->SetSummary(IPS_GetProperty($IOId, 'Port'));
            } else {
                $config = json_decode(IPS_GetConfiguration($IOId), true);
                if (array_key_exists('Port', $config)) {
                    $this->SetSummary($config['Port']);
                } elseif (array_key_exists('Host', $config)) {
                    $this->SetSummary($config['Host']);
                } elseif (array_key_exists('Address', $config)) {
                    $this->SetSummary($config['Address']);
                } elseif (array_key_exists('Name', $config)) {
                    $this->SetSummary($config['Name']);
                }
                $this->SetSummary('see ' . $IOId);
            }
        } else {
            $this->SetSummary('(none)');
        }
    }

    /**
     * IOChangeState
     *
     * Wird ausgeführt wenn sich der Status vom Parent ändert.
     *
     * @param  int $State
     * @return void
     */
    protected function IOChangeState(int $State): void
    {
        $this->Buffer = '';
        $this->InitRun = false;
        $this->Connected = false;
        $this->SetReplyEvent = false;
        // Wenn der IO Aktiv wurde
        if ($State == IS_ACTIVE) {
            $this->DoInit();
        } else { // und wenn nicht
            $this->SetStatus(IS_INACTIVE);
        }
    }

    /**
     * SendDataToParent
     *
     * @param  string $Data
     * @return string
     */
    protected function SendDataToParent(string $Data): string
    {
        if (!$this->HasActiveParent()) {
            throw new Exception($this->Translate('Instance has no active parent.'), E_USER_NOTICE);
        }
        return parent::SendDataToParent(json_encode(['DataID' => '{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}', 'Buffer' => bin2hex($Data)]));
    }

    //################# PRIVATE

    /**
     * SendCommand
     *
     * Sendet ein Command an den Controller.
     *
     * @param string $Data Der Binäre Command-String
     *
     * @return bool True bei erfolg, sonst false.
     */
    private function SendCommand(string $Data): bool
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
                $this->SendDataToParent($this->AddCRC16($Data));
            } catch (Exception $exc) {
                $this->unlock('SendCommand');
                trigger_error($exc->getMessage(), $exc->getCode());
                return false;
            }

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
                $this->SendDebug('Timeout', '', 1);
                $this->Connected = false;
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
     * DoInit
     *
     * Initialisiert den Controller und setzt die Statusvariablen auf einen definierten Wert.
     *
     * @return bool True bei Erfolg, sonst false.
     */
    private function DoInit(): bool
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

        $this->SetValue('STATE', true);
        $data = chr(0x0B) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data)) {
            $this->SetValue('Speed', 0);
        }
        $data = chr(0x0C) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data)) {
            $this->SetValue('Brightness', 1);
        }
        $data = chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data)) {
            $this->SetValue('Color', 0);
        }
        $this->SetValue('Play', 3);
        $this->SetValue('Program', 1);
        $this->SendDebug('End Init', 'Instance', 0);

        return true;
    }

    /**
     * SendInit
     *
     * Sendet die Initialisierung an den Controller und prüft die Rückmeldung.
     *
     * @throws Exception Wenn kein aktiver Parent verbunden ist.
     *
     * @return bool True bei Erfolg, sonst false.
     */
    private function SendInit(): bool
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
                $this->SendDataToParent(chr(0xFD));
            } catch (Exception $exc) {
                $this->InitRun = false;
                $this->Connected = false;
                throw $exc;
            }

            if ($this->WaitForResponse(250)) {    //warte auf Reply
                $Buffer = $this->Buffer;
                $this->Buffer = '';
                if ($Buffer == 'e') {
                    $this->SendDebug('Receive Sync', '', 1);
                    $InitState = true;
                    $i = 9;
                }
            }

        }
        if ($InitState) {
            $InitState = false;

            try {
                $this->SendDebug('Send', chr(0xFD) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0xCF) . chr(0x2C), 1);
                $this->SendDataToParent(chr(0xFD) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0xCF) . chr(0x2C));
            } catch (Exception $exc) {
                $this->InitRun = false;
                $this->Connected = false;

                throw $exc;
            }

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
     * AddCRC16
     *
     * Fügt dem übergebenden String eine CRC16 hinzu.
     *
     * @param string $string String aus welchem die CRC gebildet wird.
     *
     * @return string Der übergebene String mit angehängter CRC16 Checksumme.
     */
    private function AddCRC16(string $string): string
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
     * WaitForResponse
     *
     * Warte auf das SetReply Event.
     *
     * @param int $Timeout Max. Zeit in ms in der dass Event eintreffen muss.
     *
     * @return bool True wenn das Event eintrifft, false wenn Timeout erreicht wurde.
     */
    private function WaitForResponse(int $Timeout): bool
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
}

/* @} */
