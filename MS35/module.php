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
        $this->CreateProfil();

        $this->RegisterVariableBoolean('STATE', 'STATE', '~Switch', 1);
        $this->EnableAction('STATE');
        $this->RegisterVariableInteger('Color', 'Color', '~HexColor', 2);
        $this->EnableAction('Color');
        $this->RegisterVariableInteger('Program', 'Program', 'MS35.Program', 3);
        $this->EnableAction('Program');
        $this->RegisterVariableInteger('Play', 'Play', 'MS35.PrgStatus', 4);
        $this->EnableAction('Play');
        $this->RegisterVariableInteger('Speed', 'Speed', 'MS35.Speed', 5);
        $this->EnableAction('Speed');
        $this->RegisterVariableInteger('Brightness', 'Brightness', 'MS35.Brightness', 6);
        $this->EnableAction('Brightness');
//        $this->ConnectParent("{A151ECE9-D733-4FB9-AA15-7F7DD10C58AF}");        
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
    }

    public function ReceiveData($JSONString)
    {
        IPS_LogMessage('RecData', utf8_decode($JSONString));
//        IPS_LogMessage(__CLASS__, __FUNCTION__); // 
        //FIXME Bei Status inaktiv abbrechen
        IPS_LogMessage('RecData', utf8_decode(print_r(json_decode($JSONString)), true));
    }

################## PRIVATE    

    private function CreateProfil()
    {
        //IPS_LogMessage(__CLASS__, __FUNCTION__); //            
        if (!IPS_VariableProfileExists('MS35.Program'))
        {
            IPS_CreateVariableProfile('MS35.Program', 1);
            //IPS_SetVariableProfileAssociation('Execute.HM', 0, 'Start', '', -1);
        }
        if (!IPS_VariableProfileExists('MS35.PrgStatus'))
        {
            IPS_CreateVariableProfile('MS35.PrgStatus', 1);
            //IPS_SetVariableProfileAssociation('Execute.HM', 0, 'Start', '', -1);
        }
        if (!IPS_VariableProfileExists('MS35.Speed'))
        {
            IPS_CreateVariableProfile('MS35.Speed', 1);
            //IPS_SetVariableProfileAssociation('Execute.HM', 0, 'Start', '', -1);
        }
        if (!IPS_VariableProfileExists('MS35.Brightness'))
        {
            IPS_CreateVariableProfile('MS35.Brightness', 1);
            //IPS_SetVariableProfileAssociation('Execute.HM', 0, 'Start', '', -1);
        }
    }

    private function SendInit()
    {
        for ($i = 0; $i < 9; $i++)
        {
            $result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => chr(0xFD))));
            IPS_LogMessage('ResponseData'. $this->InstanceID, utf8_decode(print_r($result, true)));
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

################## PUBLIC
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */

    public function SendSwitch($State)
    {
        
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

}

?>