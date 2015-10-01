<?

class MS35 extends IPSModule
{

    public function Create()
    {
        //Never delete this line!
        parent::Create();
//These lines are parsed on Symcon Startup or Instance creation
//You cannot use variables here. Just static values.
        /*
         *   fInitRunLock    := TCriticalSection.Create();
          fReadReplyLock  := TCriticalSection.Create();
          fReadReplyEvent := TEvent.Create(nil,false,false,'fReadyToReadReply'+inttostr(fInstanceID),true);
          fErrorLock      := TCriticalSection.Create(); */
        $this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
    }

    public function ApplyChanges()
    {
//Never delete this line!
        parent::ApplyChanges();

//        $this->RequireParent("{96A9AB3A-2538-42C5-A130-FC34205A706A}");        
        // 1. Verfügbarer SerialPort wird verbunden oder neu erzeugt, wenn nicht vorhanden.

        $this->RegisterProfileIntegerEx("MS35.Program", "Gear", "", "", Array(
            Array(1, 'Farbwechsel 1', '', -1),
            Array(2, 'Farbwechsel 2', '', -1),
            Array(3, 'Farbwechsel 3', '', -1),
            Array(4, 'Gewitter', '', -1),
            Array(5, 'Kaminfeuer', '', -1),
            Array(6, 'Sonnenauf- & untergang', '', -1),
            Array(7, 'Farbblitze', '', -1),
            Array(8, 'User 1', '', -1),
            Array(9, 'User 2', '', -1)
        ));


        $this->RegisterProfileIntegerEx("MS35.PrgStatus", "Bulb", "", "", Array(
            Array(1, 'Play', '', -1),
            Array(2, 'Pause', '', -1),
            Array(3, 'Stop', '', -1)
        ));

        $this->RegisterProfileIntegerEx("MS35.Speed", "Intensity", "", "", Array(
            Array(0, 'normal', '', -1),
            Array(1, '1/2', '', -1),
            Array(2, '1/4', '', -1),
            Array(3, '1/8', '', -1),
            Array(4, '1/16', '', -1),
            Array(5, '1/32', '', -1),
            Array(6, '1/64', '', -1),
            Array(7, '1/128', '', -1)
        ));

        $this->RegisterProfileIntegerEx("MS35.Brightness", "Sun", "", "", Array(
            Array(1, 'normal', '', -1),
            Array(2, '1/2', '', -1),
            Array(3, '1/3', '', -1)
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
//        $this->ConnectParent("{A151ECE9-D733-4FB9-AA15-7F7DD10C58AF}");        

        $this->RegisterVariableString("BufferIN", "BufferIN", "", -4);
        $this->RegisterVariableBoolean("ReplyEvent", "ReplyEvent", "", -5);
        $this->RegisterVariableBoolean("Connected", "Connected", "", -3);
        IPS_SetHidden($this->GetIDForIdent('BufferIN'), true);
        IPS_SetHidden($this->GetIDForIdent('ReplyEvent'), true);
        IPS_SetHidden($this->GetIDForIdent('Connected'), true);

        //prüfen ob IO ein SerialPort ist
//        
        // Zwangskonfiguration des SerialPort, wenn vorhanden und verbunden
        $ParentID = $this->GetParent();

        if (!($ParentID === false))
        {

            $ParentInstance = IPS_GetInstance($ParentID);
            if ($ParentInstance['ModuleInfo']['ModuleID'] == '{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}')
            {
                if (IPS_GetProperty($ParentID, 'StopBits') <> '1')
                    IPS_SetProperty($ParentID, 'StopBits', '1');
                if (IPS_GetProperty($ParentID, 'BaudRate') <> '38400')
                    IPS_SetProperty($ParentID, 'BaudRate', '38400');
                if (IPS_GetProperty($ParentID, 'Parity') <> 'None')
                    IPS_SetProperty($ParentID, 'Parity', 'None');
                if (IPS_GetProperty($ParentID, 'DataBits') <> '8')
                    IPS_SetProperty($ParentID, 'DataBits', '8');
                if (IPS_HasChanges($ParentID))
                    IPS_ApplyChanges($ParentID);
            }
        }

        try
        {
            $this->DoInit();
        }
        catch (Exception $exc)
        {
            unset($exc);
        }
    }

################## PUBLIC
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */

    public function SendSwitch(boolean $State)
    {
        $OldState = GetValueBoolean($this->GetIDForIdent('STATE'));
        if ($State) //Einschalten
        {
            if (!$OldState)
                $this->DoInit();
        }
        else //Ausschalten
        {
            $data = chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);  // farbe weg
            if ($this->SendCommand($data))
            {
                $this->SetValueBoolean('STATE', false);
                $this->SetValueInteger('Color', 0);
                $this->SetValueInteger('Play', 3);
                $data = chr(0x0B) . chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
                if ($this->SendCommand($data))
                    $this->SetValueInteger('Speed', 0);
                $data = chr(0x0C) . chr(01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
                if ($this->SendCommand($data))
                    $this->SetValueInteger('Brightness', 1);
                $data = chr(0x01) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
                $this->SendCommand($data);
            }
        }
    }

    public function SetRGB(integer $Red, integer $Green, integer $Blue)
    {
        if (($Red < 0) or ( $Red > 255) or ( $Green < 0) or ( $Green > 255) or ( $Blue < 0) or ( $Blue > 255))
            throw new Exception('Invalid Parameterset');
        $Data = chr(01) . chr(00) . chr($Red) . chr($Green) . chr($Blue) . chr(00) . chr(00);
        $Color = ($Red << 16) + ($Green << 8) + $Blue;
//        IPS_LogMessage('Color',print_r((string)$Color,1));
//        IPS_LogMessage('Color', bin2hex($Color));        
        if ($this->SendCommand($Data))
        {
            $this->SetValueInteger('Color', $Color);
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Play', 3);
        }
    }

    public function Play()
    {
        $Data = chr(0x0A) . chr(0x07) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Play'
        if ($this->SendCommand($Data))
        {
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Play', 1);
        }
    }

    public function Pause()
    {
        $Data = chr(0x0A) . chr(0x06) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00); //'Pause'
        if ($this->SendCommand($Data))
        {
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Play', 2);
        }
    }

    public function Stop()
    {
        $Color = GetValueInteger($this->GetIDForIdent('Color'));
        $Red = ($Color & 0x00ff0000) >> 16;
        $Green = ($Color & 0x0000ff00) >> 8;
        $Blue = $Color & 0x000000ff;
        $Data = chr(01) . chr(00) . chr($Red) . chr($Green) . chr($Blue) . chr(00) . chr(00);
        if ($this->SendCommand($Data))
        {
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Play', 3); //stop
        }
    }

    public function RunProgram(integer $Programm)
    {
        if (($Programm < 1) or ( $Programm > 9))
            throw new Exception('Invalid Program-Index');

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
        if ($this->SendCommand($data[$Programm - 1]))
        {
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Program', $Programm);
            $this->SetValueInteger('Play', 1); //play
            $wait = true;
            if (($Programm == 4) or ( $Programm == 5))
            {
                $this->SetValueInteger('Speed', 0);
            }
            else
            {
                $Speed = GetValueInteger($this->GetIDForIdent('Speed'));
                if (($Speed < 0) or ( $Speed > 8))
                    $this->SetValueInteger('Speed', 0);
                else
                {
                    if ($Speed <> 0)
                    {
                        $send = chr(0x0B) . chr(intval(pow(2, $Speed))) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
                        IPS_Sleep(400);
                        $wait = false;
                        $this->SendCommand($send);
                    }
                }
            }
            $Brightness = GetValueInteger($this->GetIDForIdent('Brightness'));
            if (($Brightness < 1) or ( $Brightness > 3))
            {
                $this->SetValueInteger('Brightness', 1);
            }
            else
            {
                if ($Brightness <> 1)
                {
                    $send = chr(0x0C) . chr(value) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
                    if ($wait)
                        IPS_Sleep(400);
                    $this->SendCommand($send);
                }
            }
        }
    }

    public function SetSpeed(integer $Speed)
    {
        if (($Speed < 0) or ( $Speed > 8))
            throw new Exception('Invalid Speed-Level');
        $Program = GetValueInteger($this->GetIDForIdent('Program'));
        if (($Program <> 4) and ( $Program <> 5))
        {
            $data = chr(0x0B) . chr(intval(pow(2, $Speed))) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
            if ($this->SendCommand($data))
                $this->SetValueInteger('Speed', $Speed);
        }
    }

    public function SetBrightness(integer $Level)
    {
        if (($Level < 1) or ( $Level > 3))
            throw new Exception('Invalid Brightness-Level');
        $data = chr(0x0C) . chr($Level) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
        if ($this->SendCommand($data))
            $this->SetValueInteger('Brightness', $Level);
    }

    public function SetProgram(integer $Programm, string $Data)
    {
        if (($Programm < 8) or ( $Programm > 9))
            throw new Exception('Invalid Program-Index');

        $PrgData = json_decode($Data);
        if ($PrgData == NULL)
            throw new Exception('Error in Program-Data');

        if ($Programm == 8)
            $Programm = 2;
        if ($Programm == 9)
            $Programm = 4;

        $i = count($PrgData);
        if (($i < 1) or ( $i > 51))
            throw new Exception('Error in Program-Data');

        $this->SendCommand(chr($Programm) . chr($i) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0));
        $Programm++;

        foreach ($PrgData as $i => $Slot)
        {
            $Red = $Slot->R;
            $Green = $Slot->G;
            $Blue = $Slot->B;
            $Fade = $Slot->F;
            $Hold = $Slot->H;
            if (($Red < 0) or ( $Red > 255) or ( $Green < 0) or ( $Green > 255) or ( $Blue < 0) or ( $Blue > 255) or ( $Fade < 0) or ( $Fade > 255) or ( $Hold < 0) or ( $Hold > 255))
                throw new Exception('Error in Program-Data');
            $this->SendCommand(chr($Programm) . chr($i + 1) . chr($Red) . chr($Green) . chr($Blue) . chr($Fade) . chr($Hold));
        }
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
//IPS_LogMessage(__CLASS__, __FUNCTION__ . ' Ident:.' . $Ident); //     
//unset($Value);
        switch ($Ident)
        {
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
                switch ($Value)
                {
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
                        throw new Exception('Invalid Value');
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
                throw new Exception('Invalid Ident');
                break;
        }
    }

################## PRIVATE    

    private function SendCommand($Data)
    {
        if (!$this->lock('InitRun'))
            return;
        else
            $this->unlock('InitRun');
        if ($this->GetErrorState())
            if (!$this->SendInit())
                return;
        $BufferID = $this->GetIDForIdent("BufferIN");
        if ($this->lock('SendCommand'))
        {
            try
            {
                $sendok = $this->SendDataToParent($this->AddCRC16($Data));
            }
            catch (Exception $exc)
            {
                $this->unlock('SendCommand');
                throw new $exc;
            }
            if ($sendok)
            {
                if ($this->WaitForResponse(1000))    //warte auf Reply
                {
                    $Buffer = GetValueString($BufferID);
                    SetValueString($BufferID, '');
                    $this->SetReplyEvent(FALSE);
                    IPS_LogMessage('Buffer', print_r($Buffer, 1));
                    if ($Buffer == 'a')
                    {
                        //Sleep(25);
                        $this->unlock('SendCommand');
                        return true;
                    }
                    else
                    {

                        //Senddata('Error','NACK');
                        SetValueString($BufferID, '');
                        $this->SetErrorState(true);
                        $this->unlock('SendCommand');
                        throw new Exception('Controller send NACK.');
                    }
                }
                else
                {
                    //Senddata('Error','Timeout');
                    $this->SetErrorState(true);
                    $this->unlock('SendCommand');
                    throw new Exception('Controller do not response.');
                }
            }
            else
            {
                $this->unlock('SendCommand');
                throw new Exception('Controller do not response.');
            }
        }
        else
        {
            throw new Exception('SendCommand is blocked.');
        }
    }

    private function DoInit()
    {
        try
        {
            $ret = $this->SendInit();
        }
        catch (Exception $exc)
        {
            throw new Exception($exc);
        }
        if (!$ret)
            return false;


        $this->SetValueBoolean('STATE', true);
        $data = chr(0x0B) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data))
            $this->SetValueInteger('Speed', 0);
        $data = chr(0x0C) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data))
            $this->SetValueInteger('Brightness', 1);
        $data = chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
        if ($this->SendCommand($data))
            $this->SetValueInteger('Color', 0);
        $this->SetValueInteger('Play', 3);
        $this->SetValueInteger('Program', 1);
        return true;
    }

    private function SendInit()
    {
        if (!$this->lock('InitRun'))
            return false;
        $InitState = false;
        $BufferID = $this->GetIDForIdent("BufferIN");
//        $Text = chr(0x0D);
        for ($i = 0; $i < 9; $i++)
        {
            try
            {
                $sendok = $this->SendDataToParent(chr(0xFD));
            }
            catch (Exception $exc)
            {
                $this->unlock('InitRun');
                throw new $exc;
            }
            if ($sendok)
            {
                if ($this->WaitForResponse(250))    //warte auf Reply
                {
                    $Buffer = GetValueString($BufferID);
                    SetValueString($BufferID, '');
                    $this->SetReplyEvent(FALSE);
                    if ($Buffer == 'e')
                    {
                        $InitState = true;
                        $i = 9;
                    }
                }
            }
            else
            {
                $i = 9;
            }
        }
        if ($InitState)
        {
            $InitState = false;
            try
            {
                $sendok = $this->SendDataToParent(chr(0xFD) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0xCF) . chr(0x2C));
            }
            catch (Exception $exc)
            {
                $this->unlock('InitRun');
                throw new $exc;
            }
            
            if ($sendok)
            {
                $Buffer = '';

                for ($i = 0; $i < 4; $i++)
                {
                    if ($this->WaitForResponse(500))    //warte auf Reply
                    {
                        $Buffer.= GetValueString($BufferID);
                        $this->SetReplyEvent(FALSE);

                        if (strpos($Buffer, 'C_RGB'))
                        {
                            $InitState = true;
                            $i = 4;
                        }
                    }
                }
            }
        }

        if ($InitState)
        {
            $this->SetErrorState(false);
            $this->unlock('InitRun');
            return true;
        }
        $this->SetErrorState(true);
        $this->unlock('InitRun');
        throw new Exception('Could not initialize Controller');
    }

    private function AddCRC16($string)
    {
        $crc = 0;
        for ($x = 0; $x < strlen($string); $x++)
        {

            $crc = $crc ^ ord($string[$x]);
            for ($y = 0; $y < 8; $y++)
            {

                if (($crc & 0x0001) == 0x0001)
                    $crc = ( ($crc >> 1 ) ^ 0xA001 );
                else
                    $crc = $crc >> 1;
            }
        }
        $high_byte = ($crc & 0xff00) / 256;
        $low_byte = $crc & 0x00ff;

        $string = $string . chr($high_byte) . chr($low_byte);
        return $string;
    }

    /*
      $crc = 0xFFFF;
      for ($i = 0; $i < strlen($data); $i++)
      {
      $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
      $x ^= $x >> 4;
      $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
      }
      $ret = $data.pack('N',$crc);
      return $ret;
      } */

    private function GetErrorState()
    {
        return !GetValueBoolean($this->GetIDForIdent('Connected'));
    }

    private function SetErrorState($Value)
    {
        SetValueBoolean($this->GetIDForIdent('Connected'), !$Value);
    }

    private function SetReplyEvent($Value)
    {
        $EventID = $this->GetIDForIdent('ReplyEvent');
        if ($this->lock('ReplyEvent'))
        {
            SetValueBoolean($EventID, $Value);
            $this->unlock('ReplyEvent');
            return true;
        }
        return false;
    }

    private function WaitForResponse($Timeout)
    {
        $Event = $this->GetIDForIdent('ReplyEvent');
        for ($i = 0; $i < $Timeout / 5; $i++)
        {
            if (!GetValueBoolean($Event))
                IPS_Sleep(5);
            else
            {
                return true;
            }
        }
        return false;
    }

################## DATAPOINTS

    public function ReceiveData($JSONString)
    {
//        IPS_LogMessage('RecData', utf8_decode($JSONString));
//        IPS_LogMessage(__CLASS__, __FUNCTION__); // 
//FIXME Bei Status inaktiv abbrechen
        $data = json_decode($JSONString);
                if ($data->DataID <> '{018EF6B5-AB94-40C6-AA53-46943E824ACF}')
            return false;

        $BufferID = $this->GetIDForIdent("BufferIN");
// Empfangs Lock setzen
        if (!$this->lock("ReplyLock"))
        {
            throw new Exception("ReceiveBuffer is locked");
        }
        /*
          // Datenstream zusammenfügen
          $Head = GetValueString($BufferID); */
// Stream zusammenfügen
        SetValueString($BufferID, utf8_decode($data->Buffer));
// Empfangs Event setzen
        /*        if (!$this->SetReplyEvent(TRUE))
          {
          // Empfangs Lock aufheben
          $this->unlock("ReplyLock");
          throw new Exception("Can not send to ParentLMS");
          } */
        $this->SetReplyEvent(TRUE);
// Empfangs Lock aufheben
        $this->unlock("ReplyLock");
        return true;
    }

    protected function SendDataToParent($Data)
    {
//Semaphore setzen
        if (!$this->HasActiveParent())
            throw new Exception("Instance has no active Parent.");
        if (!$this->lock("ToParent"))
        {
            throw new Exception("Can not send to Parent");
        }
// Daten senden
        try
        {
            IPS_SendDataToParent($this->InstanceID, json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Data))));
        }
        catch (Exception $exc)
        {
// Senden fehlgeschlagen

            $this->unlock("ToParent");
            throw new Exception($exc);
        }
        $this->unlock("ToParent");
        return true;
    }

################## SEMAPHOREN Helper  - private  

    private function lock($ident)
    {
        for ($i = 0; $i < 100; $i++)
        {
            if (IPS_SemaphoreEnter("MS35_" . (string) $this->InstanceID . (string) $ident, 1))
            {
//                IPS_LogMessage((string)$this->InstanceID,"Lock:LMS_" . (string) $this->InstanceID . (string) $ident);
                return true;
            }
            else
            {
                IPS_Sleep(mt_rand(1, 5));
            }
        }
        return false;
    }

    private function unlock($ident)
    {
//                IPS_LogMessage((string)$this->InstanceID,"Unlock:LMS_" . (string) $this->InstanceID . (string) $ident);

        IPS_SemaphoreLeave("MS35_" . (string) $this->InstanceID . (string) $ident);
    }

################## DUMMYS / WOARKAROUNDS - protected

    private function SetValueBoolean($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        SetValueBoolean($id, $value);
    }

    private function SetValueInteger($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        SetValueInteger($id, $value);
    }

    /*    private function SetValueString($Ident, $value)
      {
      $id = $this->GetIDForIdent($Ident);
      SetValueString($id, $value);
      } */

    protected function HasActiveParent()
    {
        $instance = IPS_GetInstance($this->InstanceID);
        if ($instance['ConnectionID'] > 0)
        {
            $parent = IPS_GetInstance($instance['ConnectionID']);
            if ($parent['InstanceStatus'] == 102)
                return true;
        }
        return false;
    }

    protected function GetParent()
    {
        $instance = IPS_GetInstance($this->InstanceID);
        return ($instance['ConnectionID'] > 0) ? $instance['ConnectionID'] : false;
    }

    /*
      protected function SetStatus($data)
      {
      IPS_LogMessage(__CLASS__, __FUNCTION__); //
      }

      protected function RegisterTimer($data, $cata)
      {
      IPS_LogMessage(__CLASS__, __FUNCTION__); //
      }

      protected function SetTimerInterval($data, $cata)
      {
      IPS_LogMessage(__CLASS__, __FUNCTION__); //
      }

      protected function LogMessage($data, $cata)
      {

      }

      protected function SetSummary($data)
      {
      IPS_LogMessage(__CLASS__, __FUNCTION__ . "Data:" . $data); //
      }
     */

//Remove on next Symcon update
    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
    {

        if (!IPS_VariableProfileExists($Name))
        {
            IPS_CreateVariableProfile($Name, 1);
        }
        else
        {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 1)
                throw new Exception("Variable profile type does not match for profile " . $Name);
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
    }

    protected function RegisterProfileIntegerEx($Name, $Icon, $Prefix, $Suffix, $Associations)
    {
        if (sizeof($Associations) === 0)
        {
            $MinValue = 0;
            $MaxValue = 0;
        }
        else
        {
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[sizeof($Associations) - 1][0];
        }

        $this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, 0);

        foreach ($Associations as $Association)
        {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }
    }

}

?>