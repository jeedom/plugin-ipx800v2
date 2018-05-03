<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class ipx800v2_relai extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
	public function postInsert()
	{
        $state = $this->getCmd(null, 'state');
        if ( ! is_object($state) ) {
            $state = new ipx800v2_relaiCmd();
			$state->setName('Etat');
			$state->setEqLogic_id($this->getId());
			$state->setType('info');
			$state->setSubType('binary');
			$state->setLogicalId('state');
			$state->setEventOnly(1);
			$state->setDisplay('generic_type','LIGHT_STATE');
			$state->setTemplate('dashboard', 'light');
			$state->setTemplate('mobile', 'light');      
			$state->save();
		}
        $btn_on = $this->getCmd(null, 'btn_on');
        if ( ! is_object($btn_on) ) {
            $btn_on = new ipx800v2_relaiCmd();
			$btn_on->setName('On');
			$btn_on->setEqLogic_id($this->getId());
			$btn_on->setType('action');
			$btn_on->setSubType('other');
			$btn_on->setLogicalId('btn_on');
			$btn_on->setEventOnly(1);
			$btn_on->setDisplay('generic_type','LIGHT_ON');
			$btn_on->save();
		}
        $btn_off = $this->getCmd(null, 'btn_off');
        if ( ! is_object($btn_off) ) {
            $btn_off = new ipx800v2_relaiCmd();
			$btn_off->setName('Off');
			$btn_off->setEqLogic_id($this->getId());
			$btn_off->setType('action');
			$btn_off->setSubType('other');
			$btn_off->setLogicalId('btn_off');
			$btn_off->setEventOnly(1);
			$btn_off->setDisplay('generic_type','LIGHT_OFF');
			$btn_off->save();
		}
        $commute = $this->getCmd(null, 'commute');
        if ( ! is_object($commute) ) {
            $commute = new ipx800v2_relaiCmd();
			$commute->setName('Commute');
			$commute->setEqLogic_id($this->getId());
			$commute->setType('action');
			$commute->setSubType('other');
			$commute->setLogicalId('commute');
			$commute->setEventOnly(1);
			$commute->setDisplay('generic_type','LIGHT_TOGGLE');
			$commute->save();
		}
        $impulsion = $this->getCmd(null, 'impulsion');
        if ( ! is_object($impulsion) ) {
            $impulsion = new ipx800v2_relaiCmd();
			$impulsion->setName('Impulsion');
			$impulsion->setEqLogic_id($this->getId());
			$impulsion->setType('action');
			$impulsion->setSubType('other');
			$impulsion->setLogicalId('impulsion');
			$impulsion->setEventOnly(1);
			$impulsion->setDisplay('generic_type','GENERIC_ACTION');
			$impulsion->save();
		}
	}

	public function postUpdate()
	{
        $switch = $this->getCmd(null, 'switch');
        if ( is_object($switch) ) {
			$switch->remove();
		}
        $impulsion = $this->getCmd(null, 'impulsion');
        if ( ! is_object($impulsion) ) {
            $impulsion = new ipx800v2_relaiCmd();
			$impulsion->setName('Impulsion');
			$impulsion->setEqLogic_id($this->getId());
			$impulsion->setType('action');
			$impulsion->setSubType('other');
			$impulsion->setLogicalId('impulsion');
			$impulsion->setEventOnly(1);
			$impulsion->setDisplay('generic_type','GENERIC_ACTION');
			$impulsion->save();
		}
		else
		{
			if ( $impulsion->getDisplay('generic_type') == "" )
			{
				$impulsion->setDisplay('generic_type','GENERIC_ACTION');
				$impulsion->save();
			}
		}
        $state = $this->getCmd(null, 'etat');
        if ( is_object($state) ) {
			$state->setLogicalId('state');
			$state->save();
		}
        $state_old = $this->getCmd(null, 'state');
        if ( is_object($state_old) && get_class ($state_old) != "ipx800v2_relaiCmd" ) {
            $state = new ipx800v2_relaiCmd();
			$state->setName($state_old->getName());
			$state->setEqLogic_id($this->getId());
			$state->setType('info');
			$state->setSubType('binary');
			$state->setLogicalId('state');
			$state->setEventOnly(1);
			$state->setIsHistorized($state_old->getIsHistorized());
			$state->setIsVisible($state_old->getIsVisible());
			$state->setDisplay('generic_type','LIGHT_STATE');
			$state->setTemplate('dashboard', 'light');
			$state->setTemplate('mobile', 'light');      
			$state->save();
			$state_old->remove();
		}
		elseif ( is_object($state_old) )
		{
			if ( $state_old->getDisplay('generic_type') == "" )
			{
				$state_old->setDisplay('generic_type','LIGHT_STATE');
				$state_old->save();
			}			
			if ( $state_old->getTemplate('dashboard') == "" )
			{
				$state_old->setTemplate('dashboard', 'light');
				$state_old->save();
			}			
			if ( $state_old->getTemplate('mobile') == "" )
			{
				$state_old->setTemplate('mobile', 'light');
				$state_old->save();
			}			
		}
        $btn_on_old = $this->getCmd(null, 'btn_on');
        if ( is_object($btn_on_old) && get_class ($btn_on_old) != "ipx800v2_relaiCmd" ) {
            $btn_on = new ipx800v2_relaiCmd();
			$btn_on->setName($btn_on_old->getName());
			$btn_on->setEqLogic_id($this->getId());
			$btn_on->setType('action');
			$btn_on->setSubType('other');
			$btn_on->setLogicalId('btn_on');
			$btn_on->setEventOnly(1);
			$btn_on->setIsHistorized($btn_on_old->getIsHistorized());
			$btn_on->setIsVisible($btn_on_old->getIsVisible());
			$btn_on->save();
			$btn_on->setDisplay('generic_type','LIGHT_ON');
			$btn_on_old->remove();
		}
 		elseif ( is_object($btn_on_old) )
		{
			if ( $btn_on_old->getDisplay('generic_type') == "" )
			{
				$btn_on_old->setDisplay('generic_type','LIGHT_ON');
				$btn_on_old->save();
			}			
		}
        $btn_off_old = $this->getCmd(null, 'btn_off');
        if ( is_object($btn_off_old) && get_class ($btn_off_old) != "ipx800v2_relaiCmd" ) {
            $btn_off = new ipx800v2_relaiCmd();
			$btn_off->setName($btn_off_old->getName());
			$btn_off->setEqLogic_id($this->getId());
			$btn_off->setType('action');
			$btn_off->setSubType('other');
			$btn_off->setLogicalId('btn_off');
			$btn_off->setEventOnly(1);
			$btn_off->setIsHistorized($btn_off_old->getIsHistorized());
			$btn_off->setIsVisible($btn_off_old->getIsVisible());
			$btn_off->setDisplay('generic_type','LIGHT_OFF');
			$btn_off->save();
			$btn_off_old->remove();
		}
 		elseif ( is_object($btn_off_old) )
		{
			if ( $btn_off_old->getDisplay('generic_type') == "" )
			{
				$btn_off_old->setDisplay('generic_type','LIGHT_OFF');
				$btn_off_old->save();
			}			
		}
        $commute_old = $this->getCmd(null, 'commute');
        if ( is_object($commute_old) && get_class ($commute_old) != "ipx800v2_relaiCmd" ) {
            $commute = new ipx800v2_relaiCmd();
			$commute->setName($commute_old->getName());
			$commute->setEqLogic_id($this->getId());
			$commute->setType('action');
			$commute->setSubType('other');
			$commute->setLogicalId('commute');
			$commute->setEventOnly(1);
			$commute->setIsHistorized($commute_old->getIsHistorized());
			$commute->setIsVisible($commute_old->getIsVisible());
			$commute->setDisplay('generic_type','LIGHT_TOGGL');
			$commute->save();
			$commute_old->remove();
		}
 		elseif ( is_object($commute_old) )
		{
			if ( $commute_old->getDisplay('generic_type') == "" )
			{
				$commute_old->setDisplay('generic_type','LIGHT_TOGGL');
				$commute_old->save();
			}			
		}
	}

	public function preInsert()
	{
		$gceid = substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2);
		$this->setEqType_name('ipx800v2_relai');
		$this->setIsEnable(0);
		$this->setIsVisible(0);
	}

    public static function event() {
        $cmd = ipx800v2_relaiCmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
        }
		if ($cmd->execCmd() != $cmd->formatValue(init('value'))) {
			$cmd->setCollectDate('');
			$cmd->event(init('value'));
		}
    }

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=ipx800v2&m=ipx800v2&id=' . $this->getId();
    }
    /*     * **********************Getteur Setteur*************************** */
}

class ipx800v2_relaiCmd extends cmd 
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function execute($_options = null) {
		log::add('ipx800v2','debug','execute '.$_options);
		$eqLogic = $this->getEqLogic();
        if (!is_object($eqLogic) || $eqLogic->getIsEnable() != 1) {
            throw new Exception(__('Equipement desactivé impossible d\éxecuter la commande : ' . $this->getHumanName(), __FILE__));
        }
		$IPXeqLogic = eqLogic::byId(substr ($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")));
		$gceid = substr($eqLogic->getLogicalId(), strpos($eqLogic->getLogicalId(),"_")+2);
		$url = $IPXeqLogic->getUrl();
		if ( $this->getLogicalId() == 'btn_on' )
			$url .= 'preset.htm?led'.($gceid+1).'=1';
		else if ( $this->getLogicalId() == 'btn_off' )
			$url .= 'preset.htm?led'.($gceid+1).'=0';
		else if ( $this->getLogicalId() == 'impulsion' )
			$url .= 'preset.htm?RLY'.($gceid+1).'=1';
		else if ( $this->getLogicalId() == 'commute' )
			$url .= 'leds.cgi?led='.$gceid;
		else
			return false;
			
		$result = @file_get_contents($url);
		log::add('ipx800v2','debug',"get ".preg_replace("/:[^:]*@/", ":XXXX@", $url));
		$count = 0;
		while ( $result === false && $count < 3 ) {
			$result = @file_get_contents($url);
			$count++;
		}
		if ( $result === false ) {
			throw new Exception(__('L\'ipx ne repond pas.',__FILE__)." ".$IPXeqLogic->getName());
		}
        return false;
    }

    public function imperihomeCmd() {
 		if ( $this->getLogicalId() == 'state' ) {
			return true;
		}
		elseif ( $this->getLogicalId() == 'impulsion' ) {
			return true;
		}
		elseif ( $this->getLogicalId() == 'commute' ) {
			return true;
		}
		else {
			return false;
		}
    }

	public function imperihomeGenerate($ISSStructure) {
		if ( $this->getLogicalId() == 'state' ) { // Sauf si on est entrain de traiter la commande "Mode", à ce moment là on indique un autre type
			$type = 'DevSwitch'; // Le type Imperihome qui correspond le mieux à la commande
		}
		elseif ( $this->getLogicalId() == 'impulsion' ) { // Sauf si on est entrain de traiter la commande "Mode", à ce moment là on indique un autre type
			$type = 'DevSwitch'; // Le type Imperihome qui correspond le mieux à la commande
		}
		elseif ( $this->getLogicalId() == 'commute' ) { // Sauf si on est entrain de traiter la commande "Mode", à ce moment là on indique un autre type
			$type = 'DevSwitch'; // Le type Imperihome qui correspond le mieux à la commande
		}
		else {
			return $info_device;
		}
		$eqLogic = $this->getEqLogic(); // Récupération de l'équipement de la commande
		$object = $eqLogic->getObject(); // Récupération de l'objet de l'équipement

		// Construction de la structure de base
		$info_device = array(
		'id' => $this->getId(), // ID de la commande, ne pas mettre autre chose!
		'name' => $eqLogic->getName()." - ".$this->getName(), // Nom de l'équipement que sera affiché par Imperihome: mettre quelque chose de parlant...
		'room' => (is_object($object)) ? $object->getId() : 99999, // Numéro de la pièce: ne pas mettre autre chose que ce code
		'type' => $type, // Type de l'équipement à retourner (cf ci-dessus)
		'params' => array(), // Le tableau des paramètres liés à ce type (qui sera complété aprés.
		);
		#$info_device['params'] = $ISSStructure[$info_device['type']]['params']; // Ici on vient copier la structure type: laisser ce code

        if (  $this->getLogicalId() == 'impulsion' ) {
			array_push ($info_device['params'], array("key" =>"pulseable", "value" => "1"));
		}
		array_push ($info_device['params'], array("value" =>  '#' . $eqLogic->getCmd(null, 'state')->getId() . '#', "key" => "status", "type" => "infoBinary", "Description" => "Current status : 1 = On / 0 = Off"));
		$info_device['actions']["setStatus"]["item"]["0"] = $eqLogic->getCmd(null, 'btn_off')->getId();
		$info_device['actions']["setStatus"]["item"]["1"] = $eqLogic->getCmd(null, 'btn_on')->getId();
		// Ici on traite les autres commandes (hors "Mode")
		return $info_device;
	}
   /*     * **********************Getteur Setteur*************************** */
   public function imperihomeAction($_action, $_value) {
      	$eqLogic = $this->getEqLogic();
		if ($this->getLogicalId() == 'state') {
		    if ($_value == '0') {
				$eqLogic->getCmd(null, 'btn_off')->execCmd();
		    } else {
				$eqLogic->getCmd(null, 'btn_on')->execCmd();
		    }
		}
		elseif ($this->getLogicalId() == 'commute') {
			$eqLogic->getCmd(null, 'commute')->execCmd(); 
		}
		elseif ($this->getLogicalId() == 'impulsion') {
			$eqLogic->getCmd(null, 'impulsion')->execCmd(); 
		}
   }
}
?>