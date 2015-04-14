<?
class MS35 extends IPSModule {

    public function __construct($InstanceID) {

        //Never delete this line!
        parent::__construct($InstanceID);
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        /*
         *   fInitRunLock    := TCriticalSection.Create();
  fReadReplyLock  := TCriticalSection.Create();
  fReadReplyEvent := TEvent.Create(nil,false,false,'fReadyToReadReply'+inttostr(fInstanceID),true);
  fErrorLock      := TCriticalSection.Create();
  RegisterVariable('STATE', 'STATE', vtBoolean, '~Switch', ActionHandler);
  RegisterVariable('Program', 'Program', vtInteger, 'MS35.Program', ActionHandler);
  RegisterVariable('Speed', 'Speed', vtInteger, 'MS35.Speed',ActionHandler);
  RegisterVariable('Brightness', 'Brightness', vtInteger, 'MS35.Brightness',ActionHandler);
  RegisterVariable('Play', 'Play', vtInteger, 'MS35.PrgStatus',ActionHandler);

  RegisterVariable('Color', 'Color', vtInteger, '~HexColor', ActionHandler);

  RequireParent(IIPSSerialPort, True);
         */
    }

    public function ApplyChanges() {
        //Never delete this line!
        parent::ApplyChanges();
    }

################## PRIVATE     

################## ActionHandler

    public function ActionHandler($StatusVariableIdent, $Value) {
    }

################## PUBLIC
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */

    public function SendSwitch($State) {
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

    protected function SetStatus($data) {
        IPS_LogMessage(__CLASS__, __FUNCTION__); //           
    }

    protected function RegisterTimer($data, $cata) {
        IPS_LogMessage(__CLASS__, __FUNCTION__); //           
    }

    protected function SetTimerInterval($data, $cata) {
        IPS_LogMessage(__CLASS__, __FUNCTION__); //           
    }

    protected function LogMessage($data, $cata) {
        
    }

    protected function SetSummary($data) {
        IPS_LogMessage(__CLASS__, __FUNCTION__ . "Data:" . $data); //                   
    }

}

?>