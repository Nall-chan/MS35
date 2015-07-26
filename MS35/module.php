<?

class MS35 extends IPSModule
{

    public function __construct($InstanceID)
    {

//Never delete this line!
        parent::__construct($InstanceID);
//These lines are parsed on Symcon Startup or Instance creation
//You cannot use variables here. Just static values.
        /*
         *   fInitRunLock    := TCriticalSection.Create();
          fReadReplyLock  := TCriticalSection.Create();
          fReadReplyEvent := TEvent.Create(nil,false,false,'fReadyToReadReply'+inttostr(fInstanceID),true);
          fErrorLock      := TCriticalSection.Create(); */
    }

    public function ApplyChanges()
    {
//Never delete this line!
        parent::ApplyChanges();

//        $this->RequireParent("{61051B08-5B92-472B-AFB2-6D971D9B99EE}");        

        $this->RegisterProfileIntegerEx("MS35.Program", "MS35.Program", "", "", Array(
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


        $this->RegisterProfileIntegerEx("MS35.PrgStatus", "MS35.PrgStatus", "", "", Array(
            Array(1, 'Play', '', -1),
            Array(2, 'Pause', '', -1),
            Array(3, 'Stop', '', -1)
        ));

        $this->RegisterProfileIntegerEx("MS35.Speed", "MS35.Speed", "", "", Array(
            Array(0, 'normal', '', -1),
            Array(1, '1/2', '', -1),
            Array(2, '1/4', '', -1),
            Array(3, '1/8', '', -1),
            Array(4, '1/16', '', -1),
            Array(5, '1/32', '', -1),
            Array(6, '1/64', '', -1),
            Array(7, '1/128', '', -1)
        ));

        $this->RegisterProfileIntegerEx("MS35.Brightness", "MS35.Brightness", "", "", Array(
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
    }

    ################## PUBLIC
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */

    public function SendSwitch($State)
    {
        
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
//IPS_LogMessage(__CLASS__, __FUNCTION__ . ' Ident:.' . $Ident); //     
//unset($Value);
        switch ($Ident)
        {
            case 'STATE':
                $this->SendInit();
                break;
            case 'Color':
                break;
            case 'Program':
                break;
            case 'Play':
                break;
            case 'Speed':
                break;
            case 'Brightness':
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
        if (!$this->GetErrorState())
            return;
        if (!$this->SendInit())
            return;
        $BufferID = $this->GetIDForIdent("BufferIN");
        if (!$this->SendDataToParent($this->AddCRC16($Data)))
        {
            if ($this->WaitForResponse(1000))    //warte auf Reply
            {

                $Buffer = GetValueString($BufferID);
                $this->SetReplyEvent(FALSE);
                if ($Buffer == 'a')
                {
//Sleep(25);
                    return true;
                }
                else
                {
//Senddata('Error','NACK');
                    $this->SetErrorState(true);
                    throw new Exception('Controller send NACK.');
                }
            }
            else
            {
//Senddata('Error','Timeout');
                $this->SetErrorState(true);
                throw new Exception('Controller do not response.');
            }
        }
    }

    private function DoInit()
    {
        
    }

    private function SendInit()
    {
        if (!$this->lock('InitRun'))
            return;
        $InitState = false;
        $BufferID = $this->GetIDForIdent("BufferIN");
//        $Text = chr(0x0D);
        for ($i = 0; $i < 9; $i++)
        {
            if ($this->SendDataToParent(chr(0xFD)))
            {
                if ($this->WaitForResponse(100))    //warte auf Reply
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
            if ($this->SendDataToParent(chr(0xFD) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0xCF) . chr(0x2C)))
            {
                $Buffer = '';

                for ($i = 0; $i < 4; $i++)
                {
                    if ($this->WaitForResponse(250))    //warte auf Reply
                    {
                        $Buffer.= GetValueString($BufferID);
                        $this->SetReplyEvent(FALSE);
                        if ($Buffer == 'C_RGB')
                        {
                            $InitState = true;
                            $i = 4;
                        }
                    }
                }
            }
        }
        $this->SetErrorState(!$InitState);
        if (!$InitState)
        {
            $this->unlock('InitRun');
            throw new Exception('Could not initialize Controller');
        }
        $this->unlock('InitRun');

        return true;
    }

    private function GetErrorState()
    {
        return GetValueBoolean($this->GetIDForIdent('Connected'));
    }

    private function SetErrorState($Value)
    {
        SetValueBoolean($this->GetIDForIdent('Connected'), $Value);
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

    private function WaitForResponse()
    {
        $Event = $this->GetIDForIdent('ReplyEvent');
        for ($i = 0; $i < 500; $i++)
        {
            if (!GetValueBoolean($Event))
                IPS_Sleep(10);
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
        if (!$this->SetReplyEvent(TRUE))
        {
// Empfangs Lock aufheben
            $this->unlock("ReplyLock");
            throw new Exception("Can not send to ParentLMS");
        }
// Empfangs Lock aufheben
        $this->unlock("ReplyLock");
        return true;
    }

    protected function SendDataToParent($Data)
    {
//Semaphore setzen
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
            return false;
        }
        $this->unlock("ToParent");
        return true;
    }

################## SEMAPHOREN Helper  - private  

    private function lock($ident)
    {
        for ($i = 0; $i < 100; $i++)
        {
            if (IPS_SemaphoreEnter("LMS_" . (string) $this->InstanceID . (string) $ident, 1))
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

        IPS_SemaphoreLeave("LMS_" . (string) $this->InstanceID . (string) $ident);
    }

################## DUMMYS / WOARKAROUNDS - protected

    protected function HasActiveParent()
    {
        IPS_LogMessage(__CLASS__, __FUNCTION__); //          
        $instance = IPS_GetInstance($this->InstanceID);
        if ($instance['ConnectionID'] > 0)
        {
            $parent = IPS_GetInstance($instance['ConnectionID']);
            if ($parent['InstanceStatus'] == IS_ACTIVE)
                return true;
        }
        return false;
    }

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