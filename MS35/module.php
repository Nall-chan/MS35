<?

if (@constant('IPS_BASE') == null) //Nur wenn Konstanten noch nicht bekannt sind.
{
// --- BASE MESSAGE
    define('IPS_BASE', 10000);                             //Base Message
    define('IPS_KERNELSHUTDOWN', IPS_BASE + 1);            //Pre Shutdown Message, Runlevel UNINIT Follows
    define('IPS_KERNELSTARTED', IPS_BASE + 2);             //Post Ready Message
// --- KERNEL
    define('IPS_KERNELMESSAGE', IPS_BASE + 100);           //Kernel Message
    define('KR_CREATE', IPS_KERNELMESSAGE + 1);            //Kernel is beeing created
    define('KR_INIT', IPS_KERNELMESSAGE + 2);              //Kernel Components are beeing initialised, Modules loaded, Settings read
    define('KR_READY', IPS_KERNELMESSAGE + 3);             //Kernel is ready and running
    define('KR_UNINIT', IPS_KERNELMESSAGE + 4);            //Got Shutdown Message, unloading all stuff
    define('KR_SHUTDOWN', IPS_KERNELMESSAGE + 5);          //Uninit Complete, Destroying Kernel Inteface
// --- KERNEL LOGMESSAGE
    define('IPS_LOGMESSAGE', IPS_BASE + 200);              //Logmessage Message
    define('KL_MESSAGE', IPS_LOGMESSAGE + 1);              //Normal Message                      | FG: Black | BG: White  | STLYE : NONE
    define('KL_SUCCESS', IPS_LOGMESSAGE + 2);              //Success Message                     | FG: Black | BG: Green  | STYLE : NONE
    define('KL_NOTIFY', IPS_LOGMESSAGE + 3);               //Notiy about Changes                 | FG: Black | BG: Blue   | STLYE : NONE
    define('KL_WARNING', IPS_LOGMESSAGE + 4);              //Warnings                            | FG: Black | BG: Yellow | STLYE : NONE
    define('KL_ERROR', IPS_LOGMESSAGE + 5);                //Error Message                       | FG: Black | BG: Red    | STLYE : BOLD
    define('KL_DEBUG', IPS_LOGMESSAGE + 6);                //Debug Informations + Script Results | FG: Grey  | BG: White  | STLYE : NONE
    define('KL_CUSTOM', IPS_LOGMESSAGE + 7);               //User Message                        | FG: Black | BG: White  | STLYE : NONE
// --- MODULE LOADER
    define('IPS_MODULEMESSAGE', IPS_BASE + 300);           //ModuleLoader Message
    define('ML_LOAD', IPS_MODULEMESSAGE + 1);              //Module loaded
    define('ML_UNLOAD', IPS_MODULEMESSAGE + 2);            //Module unloaded
// --- OBJECT MANAGER
    define('IPS_OBJECTMESSAGE', IPS_BASE + 400);
    define('OM_REGISTER', IPS_OBJECTMESSAGE + 1);          //Object was registered
    define('OM_UNREGISTER', IPS_OBJECTMESSAGE + 2);        //Object was unregistered
    define('OM_CHANGEPARENT', IPS_OBJECTMESSAGE + 3);      //Parent was Changed
    define('OM_CHANGENAME', IPS_OBJECTMESSAGE + 4);        //Name was Changed
    define('OM_CHANGEINFO', IPS_OBJECTMESSAGE + 5);        //Info was Changed
    define('OM_CHANGETYPE', IPS_OBJECTMESSAGE + 6);        //Type was Changed
    define('OM_CHANGESUMMARY', IPS_OBJECTMESSAGE + 7);     //Summary was Changed
    define('OM_CHANGEPOSITION', IPS_OBJECTMESSAGE + 8);    //Position was Changed
    define('OM_CHANGEREADONLY', IPS_OBJECTMESSAGE + 9);    //ReadOnly was Changed
    define('OM_CHANGEHIDDEN', IPS_OBJECTMESSAGE + 10);     //Hidden was Changed
    define('OM_CHANGEICON', IPS_OBJECTMESSAGE + 11);       //Icon was Changed
    define('OM_CHILDADDED', IPS_OBJECTMESSAGE + 12);       //Child for Object was added
    define('OM_CHILDREMOVED', IPS_OBJECTMESSAGE + 13);     //Child for Object was removed
    define('OM_CHANGEIDENT', IPS_OBJECTMESSAGE + 14);      //Ident was Changed
// --- INSTANCE MANAGER
    define('IPS_INSTANCEMESSAGE', IPS_BASE + 500);         //Instance Manager Message
    define('IM_CREATE', IPS_INSTANCEMESSAGE + 1);          //Instance created
    define('IM_DELETE', IPS_INSTANCEMESSAGE + 2);          //Instance deleted
    define('IM_CONNECT', IPS_INSTANCEMESSAGE + 3);         //Instance connectged
    define('IM_DISCONNECT', IPS_INSTANCEMESSAGE + 4);      //Instance disconncted
    define('IM_CHANGESTATUS', IPS_INSTANCEMESSAGE + 5);    //Status was Changed
    define('IM_CHANGESETTINGS', IPS_INSTANCEMESSAGE + 6);  //Settings were Changed
    define('IM_CHANGESEARCH', IPS_INSTANCEMESSAGE + 7);    //Searching was started/stopped
    define('IM_SEARCHUPDATE', IPS_INSTANCEMESSAGE + 8);    //Searching found new results
    define('IM_SEARCHPROGRESS', IPS_INSTANCEMESSAGE + 9);  //Searching progress in %
    define('IM_SEARCHCOMPLETE', IPS_INSTANCEMESSAGE + 10); //Searching is complete
// --- VARIABLE MANAGER
    define('IPS_VARIABLEMESSAGE', IPS_BASE + 600);              //Variable Manager Message
    define('VM_CREATE', IPS_VARIABLEMESSAGE + 1);               //Variable Created
    define('VM_DELETE', IPS_VARIABLEMESSAGE + 2);               //Variable Deleted
    define('VM_UPDATE', IPS_VARIABLEMESSAGE + 3);               //On Variable Update
    define('VM_CHANGEPROFILENAME', IPS_VARIABLEMESSAGE + 4);    //On Profile Name Change
    define('VM_CHANGEPROFILEACTION', IPS_VARIABLEMESSAGE + 5);  //On Profile Action Change
// --- SCRIPT MANAGER
    define('IPS_SCRIPTMESSAGE', IPS_BASE + 700);           //Script Manager Message
    define('SM_CREATE', IPS_SCRIPTMESSAGE + 1);            //On Script Create
    define('SM_DELETE', IPS_SCRIPTMESSAGE + 2);            //On Script Delete
    define('SM_CHANGEFILE', IPS_SCRIPTMESSAGE + 3);        //On Script File changed
    define('SM_BROKEN', IPS_SCRIPTMESSAGE + 4);            //Script Broken Status changed
// --- EVENT MANAGER
    define('IPS_EVENTMESSAGE', IPS_BASE + 800);             //Event Scripter Message
    define('EM_CREATE', IPS_EVENTMESSAGE + 1);             //On Event Create
    define('EM_DELETE', IPS_EVENTMESSAGE + 2);             //On Event Delete
    define('EM_UPDATE', IPS_EVENTMESSAGE + 3);
    define('EM_CHANGEACTIVE', IPS_EVENTMESSAGE + 4);
    define('EM_CHANGELIMIT', IPS_EVENTMESSAGE + 5);
    define('EM_CHANGESCRIPT', IPS_EVENTMESSAGE + 6);
    define('EM_CHANGETRIGGER', IPS_EVENTMESSAGE + 7);
    define('EM_CHANGETRIGGERVALUE', IPS_EVENTMESSAGE + 8);
    define('EM_CHANGETRIGGEREXECUTION', IPS_EVENTMESSAGE + 9);
    define('EM_CHANGECYCLIC', IPS_EVENTMESSAGE + 10);
    define('EM_CHANGECYCLICDATEFROM', IPS_EVENTMESSAGE + 11);
    define('EM_CHANGECYCLICDATETO', IPS_EVENTMESSAGE + 12);
    define('EM_CHANGECYCLICTIMEFROM', IPS_EVENTMESSAGE + 13);
    define('EM_CHANGECYCLICTIMETO', IPS_EVENTMESSAGE + 14);
// --- MEDIA MANAGER
    define('IPS_MEDIAMESSAGE', IPS_BASE + 900);           //Media Manager Message
    define('MM_CREATE', IPS_MEDIAMESSAGE + 1);             //On Media Create
    define('MM_DELETE', IPS_MEDIAMESSAGE + 2);             //On Media Delete
    define('MM_CHANGEFILE', IPS_MEDIAMESSAGE + 3);         //On Media File changed
    define('MM_AVAILABLE', IPS_MEDIAMESSAGE + 4);          //Media Available Status changed
    define('MM_UPDATE', IPS_MEDIAMESSAGE + 5);
// --- LINK MANAGER
    define('IPS_LINKMESSAGE', IPS_BASE + 1000);           //Link Manager Message
    define('LM_CREATE', IPS_LINKMESSAGE + 1);             //On Link Create
    define('LM_DELETE', IPS_LINKMESSAGE + 2);             //On Link Delete
    define('LM_CHANGETARGET', IPS_LINKMESSAGE + 3);       //On Link TargetID change
// --- DATA HANDLER
    define('IPS_DATAMESSAGE', IPS_BASE + 1100);             //Data Handler Message
    define('DM_CONNECT', IPS_DATAMESSAGE + 1);             //On Instance Connect
    define('DM_DISCONNECT', IPS_DATAMESSAGE + 2);          //On Instance Disconnect
// --- SCRIPT ENGINE
    define('IPS_ENGINEMESSAGE', IPS_BASE + 1200);           //Script Engine Message
    define('SE_UPDATE', IPS_ENGINEMESSAGE + 1);             //On Library Refresh
    define('SE_EXECUTE', IPS_ENGINEMESSAGE + 2);            //On Script Finished execution
    define('SE_RUNNING', IPS_ENGINEMESSAGE + 3);            //On Script Started execution
// --- PROFILE POOL
    define('IPS_PROFILEMESSAGE', IPS_BASE + 1300);
    define('PM_CREATE', IPS_PROFILEMESSAGE + 1);
    define('PM_DELETE', IPS_PROFILEMESSAGE + 2);
    define('PM_CHANGETEXT', IPS_PROFILEMESSAGE + 3);
    define('PM_CHANGEVALUES', IPS_PROFILEMESSAGE + 4);
    define('PM_CHANGEDIGITS', IPS_PROFILEMESSAGE + 5);
    define('PM_CHANGEICON', IPS_PROFILEMESSAGE + 6);
    define('PM_ASSOCIATIONADDED', IPS_PROFILEMESSAGE + 7);
    define('PM_ASSOCIATIONREMOVED', IPS_PROFILEMESSAGE + 8);
    define('PM_ASSOCIATIONCHANGED', IPS_PROFILEMESSAGE + 9);
// --- TIMER POOL
    define('IPS_TIMERMESSAGE', IPS_BASE + 1400);            //Timer Pool Message
    define('TM_REGISTER', IPS_TIMERMESSAGE + 1);
    define('TM_UNREGISTER', IPS_TIMERMESSAGE + 2);
    define('TM_SETINTERVAL', IPS_TIMERMESSAGE + 3);
    define('TM_UPDATE', IPS_TIMERMESSAGE + 4);
    define('TM_RUNNING', IPS_TIMERMESSAGE + 5);
// --- STATUS CODES
    define('IS_SBASE', 100);
    define('IS_CREATING', IS_SBASE + 1); //module is being created
    define('IS_ACTIVE', IS_SBASE + 2); //module created and running
    define('IS_DELETING', IS_SBASE + 3); //module us being deleted
    define('IS_INACTIVE', IS_SBASE + 4); //module is not beeing used
// --- ERROR CODES
    define('IS_EBASE', 200);          //default errorcode
    define('IS_NOTCREATED', IS_EBASE + 1); //instance could not be created
// --- Search Handling
    define('FOUND_UNKNOWN', 0);     //Undefined value
    define('FOUND_NEW', 1);         //Device is new and not configured yet
    define('FOUND_OLD', 2);         //Device is already configues (InstanceID should be set)
    define('FOUND_CURRENT', 3);     //Device is already configues (InstanceID is from the current/searching Instance)
    define('FOUND_UNSUPPORTED', 4); //Device is not supported by Module

    define('vtBoolean', 0);
    define('vtInteger', 1);
    define('vtFloat', 2);
    define('vtString', 3);
}

class MS35 extends IPSModule
{

    public function Create()
    {
        parent::Create();
        $this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

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

        $this->RegisterVariableString("BufferIN", "BufferIN", "", -4);
        $this->RegisterVariableBoolean("ReplyEvent", "ReplyEvent", "", -5);
        $this->RegisterVariableBoolean("Connected", "Connected", "", -3);
        IPS_SetHidden($this->GetIDForIdent('BufferIN'), true);
        IPS_SetHidden($this->GetIDForIdent('ReplyEvent'), true);
        IPS_SetHidden($this->GetIDForIdent('Connected'), true);

        //prüfen ob IO ein SerialPort ist
        //        
        // Zwangskonfiguration des SerialPort, wenn vorhanden und verbunden
        // Aber nie bei einem Neustart :)
        if (IPS_GetKernelRunlevel() == KR_READY)
        {
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
        }
        try
        {
            $this->DoInit();
        } catch (Exception $exc)
        {
            if (IPS_GetKernelRunlevel() == KR_READY)
                trigger_error($exc->getMessage(), $exc->getCode());
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
            {
                try
                {
                $this->DoInit();                    
                } catch (Exception $exc)
                {
                    trigger_error($exc->getMessage(),$exc->getCode());
                }


            }
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
        {
            trigger_error('Invalid Parameterset',E_USER_NOTICE);
            return false;
        }
        $Data = chr(01) . chr(00) . chr($Red) . chr($Green) . chr($Blue) . chr(00) . chr(00);
        $Color = ($Red << 16) + ($Green << 8) + $Blue;
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
        {
            trigger_error('Invalid Program-Index',E_USER_NOTICE);
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
        if ($this->SendCommand($data[$Programm - 1]))
        {
            $this->SetValueBoolean('STATE', true);
            $this->SetValueInteger('Program', $Programm);
            $this->SetValueInteger('Play', 1); //play
            $wait = true;
            if (($Programm == 4) or ( $Programm == 5))
            {
                $this->SetValueInteger('Speed', 0);
            } else
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
            } else
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
        {
            trigger_error('Invalid Speed-Level',E_USER_NOTICE);
            return false;
        }
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
        {
            trigger_error('Invalid Brightness-Level',E_USER_NOTICE);
            return false;
        }
        $data = chr(0x0C) . chr($Level) . chr(00) . chr(00) . chr(00) . chr(00) . chr(00);
        if ($this->SendCommand($data))
            $this->SetValueInteger('Brightness', $Level);
    }

    public function SetProgram(integer $Programm, string $Data)
    {
        if (($Programm < 8) or ( $Programm > 9))
        {
            trigger_error('Invalid Program-Index',E_USER_NOTICE);
            return false;
        }

        $PrgData = json_decode($Data);
        if ($PrgData == NULL)
        {
            trigger_error('Error in Program-Data',E_USER_NOTICE);
            return false;
        }

        if ($Programm == 8)
            $Programm = 2;
        if ($Programm == 9)
            $Programm = 4;

        $i = count($PrgData);
        if (($i < 1) or ( $i > 51))
        {
            trigger_error('Error in Program-Data',E_USER_NOTICE);
            return false;
        }

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
            {
                trigger_error('Error in Program-Data',E_USER_NOTICE);
                continue;
            }
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
                        trigger_error('Invalid Value',E_USER_NOTICE);
                        return;
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
                trigger_error('Invalid Ident',E_USER_NOTICE);
                break;
        }
    }

################## PRIVATE    

    private function SendCommand($Data)
    {
        if (!$this->lock('InitRun'))
            return false;
        else
            $this->unlock('InitRun');
        if ($this->GetErrorState())
            try
            {
                if (!$this->SendInit())
                    return false;
                
            } catch (Exception $exc)
            {
                trigger_error($exc->getMessage(),$exc->getCode());
                return false;
            }

        $BufferID = $this->GetIDForIdent("BufferIN");
        if ($this->lock('SendCommand'))
        {
            try
            {
                $sendok = $this->SendDataToParent($this->AddCRC16($Data));
            } catch (Exception $exc)
            {
                $this->unlock('SendCommand');
                trigger_error($exc->getMessage(),$exc->getCode());
                return false;
            }
            if ($sendok)
            {
                if ($this->WaitForResponse(1000))    //warte auf Reply
                {
                    $Buffer = GetValueString($BufferID);
                    SetValueString($BufferID, '');
                    $this->SetReplyEvent(FALSE);
//                    IPS_LogMessage('Buffer', print_r($Buffer, 1));
                    if ($Buffer == 'a')
                    {
                        //Sleep(25);
                        $this->unlock('SendCommand');
                        return true;
                    } else
                    {

                        //Senddata('Error','NACK');
                        SetValueString($BufferID, '');
                        $this->SetErrorState(true);
                        $this->unlock('SendCommand');
                        
                        trigger_error('Controller send NACK.',E_USER_NOTICE);
                        return false;
                    }
                } else
                {
                    //Senddata('Error','Timeout');
                    $this->SetErrorState(true);
                    $this->unlock('SendCommand');
                    trigger_error('Controller do not response.',E_USER_NOTICE);
                
                return false;
                    
                }
            } else
            {
                $this->unlock('SendCommand');
                trigger_error('Controller do not response.',E_USER_NOTICE);
                return false;
                
            }
        } else
        {
            trigger_error('SendCommand is blocked.',E_USER_NOTICE);
                return false;
            
        }
    }

    private function DoInit()
    {
        try
        {
            $ret = $this->SendInit();
        } catch (Exception $exc)
        {
            throw $exc;
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
            } catch (Exception $exc)
            {
                $this->unlock('InitRun');
                throw  $exc;
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
            } else
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
            } catch (Exception $exc)
            {
                $this->unlock('InitRun');
                throw $exc;
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
        throw new Exception('Could not initialize Controller',E_USER_NOTICE);
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
            
            trigger_error("ReceiveBuffer is locked",E_USER_NOTICE);
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
            throw new Exception("Instance has no active Parent.",E_USER_NOTICE);
        if (!$this->lock("ToParent"))
        {
            throw new Exception("Can not send to Parent",E_USER_NOTICE);
        }
// Daten senden
        try
        {
            IPS_SendDataToParent($this->InstanceID, json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Data))));
        } catch (Exception $exc)
        {
// Senden fehlgeschlagen

            $this->unlock("ToParent");
            throw $exc;
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
                return true;
            } else
            {
                IPS_Sleep(mt_rand(1, 5));
            }
        }
        return false;
    }

    private function unlock($ident)
    {
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


    protected function HasActiveParent()
    {
        $instance = @IPS_GetInstance($this->InstanceID);
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
        $instance = @IPS_GetInstance($this->InstanceID);
        return ($instance['ConnectionID'] > 0) ? $instance['ConnectionID'] : false;
    }

//Remove on next Symcon update
    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
    {

        if (!IPS_VariableProfileExists($Name))
        {
            IPS_CreateVariableProfile($Name, 1);
        } else
        {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 1)
                throw new Exception("Variable profile type does not match for profile " . $Name,E_USER_NOTICE);
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
        } else
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