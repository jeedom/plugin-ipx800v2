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

include_file('core', 'ipx800v2_analogique', 'class', 'ipx800v2');
include_file('core', 'ipx800v2_relai', 'class', 'ipx800v2');
include_file('core', 'ipx800v2_bouton', 'class', 'ipx800v2');
include_file('core', 'ipx800v2_compteur', 'class', 'ipx800v2');

class ipx800v2 extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

	public static function daemon() {
		$starttime = microtime (true);
		log::add('ipx800v2','debug','cron start');
		foreach (self::byType('ipx800v2') as $eqLogic) {
			$eqLogic->pull();
		}
		log::add('ipx800v2','debug','cron stop');
		$endtime = microtime (true);
		if ( $endtime - $starttime < config::byKey('temporisation_lecture', 'ipx800v2', 60, true) )
		{
			usleep(floor((config::byKey('temporisation_lecture', 'ipx800v2') + $starttime - $endtime)*1000000));
		}
	}

	public static function deamon_info() {
		$return = array();
		$return['log'] = '';
		$return['state'] = 'nok';
		$cron = cron::byClassAndFunction('ipx800v2', 'daemon');
		if (is_object($cron) && $cron->running()) {
			$return['state'] = 'ok';
		}
		$return['launchable'] = 'ok';
		return $return;
	}

	public static function deamon_start($_debug = false) {
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		}
		$cron = cron::byClassAndFunction('ipx800v2', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		$cron->run();
	}

	public static function deamon_stop() {
		$cron = cron::byClassAndFunction('ipx800v2', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		$cron->halt();
	}

	public static function deamon_changeAutoMode($_mode) {
		$cron = cron::byClassAndFunction('ipx800v2', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tâche cron introuvable', __FILE__));
		}
		$cron->setEnable($_mode);
		$cron->save();
	}

	public function getUrl() {
		$url = 'http://';
		if ( $this->getConfiguration('username') != '' )
		{
			$url .= $this->getConfiguration('username').':'.$this->getConfiguration('password').'@';
		} 
		$url .= $this->getConfiguration('ip');
		if ( $this->getConfiguration('port') != '' )
		{
			$url .= ':'.$this->getConfiguration('port');
		}
		return $url."/";
	}

	public function preUpdate()
	{
		switch ($this->getEqType_name()) {
			case "carte":
				break;
			case "bouton":
				break;
			case "analogique":
				break;
			case "relai":
				break;
			case "compteur":
				break;
		}
		if ( $this->getIsEnable() )
		{
			log::add('ipx800v2','debug','get '.preg_replace("/:[^:]*@/", ":XXXX@", $this->getUrl()). 'status.xml');
			$this->xmlstatus = @simplexml_load_file($this->getUrl(). 'status.xml');
#			if ( $this->xmlstatus === false )
#				throw new Exception(__('L\'ipx800v2 ne repond pas.',__FILE__));
		}
	}

	public function preInsert()
	{
		$this->setIsVisible(0);
	}

	public function postInsert()
	{
		$cmd = $this->getCmd(null, 'status');
		if ( ! is_object($cmd) ) {
			$cmd = new ipx800v2Cmd();
			$cmd->setName('Etat');
			$cmd->setEqLogic_id($this->getId());
			$cmd->setType('info');
			$cmd->setSubType('binary');
			$cmd->setLogicalId('status');
			$cmd->setIsVisible(1);
			$cmd->setEventOnly(1);
			$cmd->setDisplay('generic_type','GENERIC_INFO');
			$cmd->save();
		}
        $all_on = $this->getCmd(null, 'all_on');
        if ( ! is_object($all_on) ) {
            $all_on = new ipx800v2Cmd();
			$all_on->setName('All On');
			$all_on->setEqLogic_id($this->getId());
			$all_on->setType('action');
			$all_on->setSubType('other');
			$all_on->setLogicalId('all_on');
			$all_on->setEventOnly(1);
			$all_on->setDisplay('generic_type','GENERIC_ACTION');
			$all_on->save();
		}
        $all_off = $this->getCmd(null, 'all_off');
        if ( ! is_object($all_off) ) {
            $all_off = new ipx800v2Cmd();
			$all_off->setName('All Off');
			$all_off->setEqLogic_id($this->getId());
			$all_off->setType('action');
			$all_off->setSubType('other');
			$all_off->setLogicalId('all_off');
			$all_off->setEventOnly(1);
			$all_off->setDisplay('generic_type','GENERIC_ACTION');
			$all_off->save();
		}
		for ($compteurId = 0; $compteurId <= 1; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_A".$compteurId, 'ipx800v2_analogique')) ) {
				log::add('ipx800v2','debug','Creation analogique : '.$this->getId().'_A'.$compteurId);
				$eqLogic = new ipx800v2_analogique();
				$eqLogic->setLogicalId($this->getId().'_A'.$compteurId);
				$eqLogic->setName('Analogique ' . ($compteurId+1));
				$eqLogic->save();
			}
		}
		for ($compteurId = 0; $compteurId <= 7; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_R".$compteurId, 'ipx800v2_relai')) ) {
				log::add('ipx800v2','debug','Creation relai : '.$this->getId().'_R'.$compteurId);
				$eqLogic = new ipx800v2_relai();
				$eqLogic->setLogicalId($this->getId().'_R'.$compteurId);
				$eqLogic->setName('Relai ' . ($compteurId+1));
				$eqLogic->save();
			}
		}
		for ($compteurId = 0; $compteurId <= 3; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_B".$compteurId, 'ipx800v2_bouton')) ) {
				log::add('ipx800v2','debug','Creation bouton : '.$this->getId().'_B'.$compteurId);
				$eqLogic = new ipx800v2_bouton();
				$eqLogic->setLogicalId($this->getId().'_B'.$compteurId);
				$eqLogic->setName('Bouton ' . ($compteurId+1));
				$eqLogic->save();
			}
		}
		for ($compteurId = 0; $compteurId <= 1; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_C".$compteurId, 'ipx800v2_compteur')) ) {
				log::add('ipx800v2','debug','Creation compteur : '.$this->getId().'_C'.$compteurId);
				$eqLogic = new ipx800v2_compteur();
				$eqLogic->setLogicalId($this->getId().'_C'.$compteurId);
				$eqLogic->setName('Compteur ' . ($compteurId+1));
				$eqLogic->save();
			}
		}
	}

	public function postUpdate()
	{
		for ($compteurId = 0; $compteurId <= 1; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_A".$compteurId, 'ipx800v2_analogique')) ) {
				log::add('ipx800v2','debug','Creation analogique : '.$this->getId().'_A'.$compteurId);
				$eqLogic = new ipx800v2_analogique();
				$eqLogic->setLogicalId($this->getId().'_A'.$compteurId);
				$eqLogic->setName('Analogique ' . ($compteurId+1));
				$eqLogic->save();
			}
		}
		for ($compteurId = 0; $compteurId <= 7; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_R".$compteurId, 'ipx800v2_relai')) ) {
				log::add('ipx800v2','debug','Creation relai : '.$this->getId().'_R'.$compteurId);
				$eqLogic = new ipx800v2_relai();
				$eqLogic->setLogicalId($this->getId().'_R'.$compteurId);
				$eqLogic->setName('Relai ' . ($compteurId+1));
				$eqLogic->save();
			}
		}
		for ($compteurId = 0; $compteurId <= 3; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_B".$compteurId, 'ipx800v2_bouton')) ) {
				log::add('ipx800v2','debug','Creation bouton : '.$this->getId().'_B'.$compteurId);
				$eqLogic = new ipx800v2_bouton();
				$eqLogic->setLogicalId($this->getId().'_B'.$compteurId);
				$eqLogic->setName('Bouton ' . ($compteurId+1));
				$eqLogic->save();
			}
		}
		for ($compteurId = 0; $compteurId <= 1; $compteurId++) {
			if ( ! is_object(self::byLogicalId($this->getId()."_C".$compteurId, 'ipx800v2_compteur')) ) {
				log::add('ipx800v2','debug','Creation compteur : '.$this->getId().'_C'.$compteurId);
				$eqLogic = new ipx800v2_compteur();
				$eqLogic->setLogicalId($this->getId().'_C'.$compteurId);
				$eqLogic->setName('Compteur ' . ($compteurId+1));
				$eqLogic->save();
			}
		}

		$cmd = $this->getCmd(null, 'status');
		if ( ! is_object($cmd) ) {
			$cmd = new ipx800v2Cmd();
			$cmd->setName('Etat');
			$cmd->setEqLogic_id($this->getId());
			$cmd->setType('info');
			$cmd->setSubType('binary');
			$cmd->setLogicalId('status');
			$cmd->setIsVisible(1);
			$cmd->setEventOnly(1);
			$cmd->setDisplay('generic_type','GENERIC_INFO');
			$cmd->save();
		}
		else
		{
			if ( $cmd->getDisplay('generic_type') == "" )
			{
				$cmd->setDisplay('generic_type','GENERIC_INFO');
				$cmd->save();
			}
		}
		$all_on = $this->getCmd(null, 'all_on');
		if ( is_object($all_on)) {
			if ( $all_on->getDisplay('generic_type') == "" )
			{
				$all_on->setDisplay('generic_type','GENERIC_ACTION');
				$all_on->save();
			}
		}

		$all_off = $this->getCmd(null, 'all_off');
		if ( is_object($all_off)) {
			if ( $all_off->getDisplay('generic_type') == "" )
			{
				$all_off->setDisplay('generic_type','GENERIC_ACTION');
				$all_off->save();
			}
		}
        $reboot = $this->getCmd(null, 'reboot');
        if ( is_object($reboot) ) {
			$reboot->remove();
		}
		$ipx800v2Cmd = $this->getCmd(null, 'updatetime');
        if ( is_object($ipx800v2Cmd) ) {
			$ipx800v2Cmd->remove();
		}
	}

	public function preRemove()
	{
		foreach (self::byType('ipx800v2_compteur') as $eqLogic) {
			if ( substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
				log::add('ipx800v2','debug','Suppression compteur : '.$eqLogic->getName());
				$eqLogic->remove();
			}
		}
		foreach (self::byType('ipx800v2_analogique') as $eqLogic) {
			if ( substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
				log::add('ipx800v2','debug','Suppression analogique : '.$eqLogic->getName());
				$eqLogic->remove();
			}
		}
		foreach (self::byType('ipx800v2_relai') as $eqLogic) {
			if ( substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
				log::add('ipx800v2','debug','Suppression relai : '.$eqLogic->getName());
				$eqLogic->remove();
			}
		}
		foreach (self::byType('ipx800v2_bouton') as $eqLogic) {
			if ( substr($eqLogic->getLogicalId(), 0, strpos($eqLogic->getLogicalId(),"_")) == $this->getId() ) {
				log::add('ipx800v2','debug','Suppression bouton : '.$eqLogic->getName());
				$eqLogic->remove();
			}
		}
	}

	public function event() {
		foreach (eqLogic::byType('ipx800v2') as $eqLogic) {
			if ( $eqLogic->getId() == init('id') ) {
				$eqLogic->pull();
			}
		}
	}

	public function pull() {
		if ( $this->getIsEnable() ) {
			log::add('ipx800v2','debug','pull '.$this->getName());
			$statuscmd = $this->getCmd(null, 'status');
			$url = $this->getUrl();
			log::add('ipx800v2','debug','get '.preg_replace("/:[^:]*@/", ":XXXX@", $url).'status.xml');
			$this->xmlstatus = @simplexml_load_file($url. 'status.xml');
			$count = 0;
			while ( $this->xmlstatus === false && $count < 3 ) {
				log::add('ipx800v2','debug','reget '.preg_replace("/:[^:]*@/", ":XXXX@", $url).'status.xml');
				$this->xmlstatus = @simplexml_load_file($url. 'status.xml');
				$count++;
			}
			if ( $this->xmlstatus === false ) {
				if ($statuscmd->execCmd() != 0) {
					$statuscmd->setCollectDate('');
					$statuscmd->event(0);
				}
				log::add('ipx800v2','error',__('L\'ipx ne repond pas.',__FILE__)." ".$this->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $url). 'status.xml');
				return false;
			}
			if ($statuscmd->execCmd() != 1) {
				$statuscmd->setCollectDate('');
				$statuscmd->event(1);
			}
			foreach (self::byType('ipx800v2_relai') as $eqLogicRelai) {
				if ( $eqLogicRelai->getIsEnable() && substr($eqLogicRelai->getLogicalId(), 0, strpos($eqLogicRelai->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicRelai->getLogicalId(), strpos($eqLogicRelai->getLogicalId(),"_")+2);
					$xpathModele = '//led'.$gceid;
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						$eqLogic_cmd = $eqLogicRelai->getCmd(null, 'state');
						if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
							log::add('ipx800v2','debug',"Change state off ".$eqLogicRelai->getName());
							$eqLogic_cmd->setCollectDate('');
							$eqLogic_cmd->event($status[0]);
						}
					}
				}
			}
			foreach (self::byType('ipx800v2_bouton') as $eqLogicBouton) {
				if ( $eqLogicBouton->getIsEnable() && substr($eqLogicBouton->getLogicalId(), 0, strpos($eqLogicBouton->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = 3- intval(substr($eqLogicBouton->getLogicalId(), strpos($eqLogicBouton->getLogicalId(),"_")+2));
					$xpathModele = '//btn'.$gceid;
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						$eqLogic_cmd = $eqLogicBouton->getCmd(null, 'state');
						if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
							log::add('ipx800v2','debug',"Change state off ".$eqLogicBouton->getName());
							$eqLogic_cmd->setCollectDate('');
							$eqLogic_cmd->event($status[0]);
						}
					}
				}
			}
			foreach (self::byType('ipx800v2_analogique') as $eqLogicAnalogique) {
				if ( $eqLogicAnalogique->getIsEnable() && substr($eqLogicAnalogique->getLogicalId(), 0, strpos($eqLogicAnalogique->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicAnalogique->getLogicalId(), strpos($eqLogicAnalogique->getLogicalId(),"_")+2);
					$xpathModele = '//an'.($gceid+1);
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						$eqLogic_cmd = $eqLogicAnalogique->getCmd(null, 'brut');
						if ($eqLogic_cmd->execCmd() != $eqLogic_cmd->formatValue($status[0])) {
							log::add('ipx800v2','debug',"Change brut off ".$eqLogicAnalogique->getName());
						}
						$eqLogic_cmd->setCollectDate('');
						$eqLogic_cmd->event($status[0]);
						$eqLogic_cmd = $eqLogicAnalogique->getCmd(null, 'reel');
						$eqLogic_cmd->event($eqLogic_cmd->execute());
					}
				}
			}
			foreach (self::byType('ipx800v2_compteur') as $eqLogicCompteur) {
				if ( $eqLogicCompteur->getIsEnable() && substr($eqLogicCompteur->getLogicalId(), 0, strpos($eqLogicCompteur->getLogicalId(),"_")) == $this->getId() ) {
					$gceid = substr($eqLogicCompteur->getLogicalId(), strpos($eqLogicCompteur->getLogicalId(),"_")+2);
					$xpathModele = '//count'.$gceid;
					$status = $this->xmlstatus->xpath($xpathModele);
					
					if ( count($status) != 0 )
					{
						$nbimpulsion_cmd = $eqLogicCompteur->getCmd(null, 'nbimpulsion');
						$nbimpulsion = $nbimpulsion_cmd->execCmd();
						$nbimpulsionminute_cmd = $eqLogicCompteur->getCmd(null, 'nbimpulsionminute');
						if ($nbimpulsion != $status[0]) {
							log::add('ipx800v2','debug',"Change nbimpulsion off ".$eqLogicCompteur->getName());
							$lastCollectDate = $nbimpulsion_cmd->getCollectDate();
							if ( $lastCollectDate == '' ) {
								log::add('ipx800v2','debug',"Change nbimpulsionminute 0");
								$nbimpulsionminute = 0;
							} else {
								$DeltaSeconde = (time() - strtotime($lastCollectDate))*60;
								if ( $DeltaSeconde != 0 )
								{
									if ( $status[0] > $nbimpulsion ) {
										$DeltaValeur = $status[0] - $nbimpulsion;
									} else {
										$DeltaValeur = $status[0];
									}
									$nbimpulsionminute = round (($status[0] - $nbimpulsion)/(time() - strtotime($lastCollectDate))*60, 6);
								} else {
									$nbimpulsionminute = 0;
								}
							}
							log::add('ipx800v2','debug',"Change nbimpulsionminute ".$nbimpulsionminute);
							$nbimpulsionminute_cmd->setCollectDate(date('Y-m-d H:i:s'));
							$nbimpulsionminute_cmd->event($nbimpulsionminute);
						} else {
							$nbimpulsionminute_cmd->setCollectDate(date('Y-m-d H:i:s'));
							$nbimpulsionminute_cmd->event(0);
						}
						$nbimpulsion_cmd->setCollectDate(date('Y-m-d H:i:s'));
						$nbimpulsion_cmd->event($status[0]);
					}
				}
			}
			log::add('ipx800v2','debug','pull end '.$this->getName());
		}
	}
    /*     * **********************Getteur Setteur*************************** */
}

class ipx800v2Cmd extends cmd 
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
    public function execute($_options = null) {
		$eqLogic = $this->getEqLogic();
        if (!is_object($eqLogic) || $eqLogic->getIsEnable() != 1) {
            throw new Exception(__('Equipement desactivé impossible d\éxecuter la commande : ' . $this->getHumanName(), __FILE__));
        }
		$url = $eqLogic->getUrl();
			
		if ( $this->getLogicalId() == 'all_on' )
		{
			$url .= 'preset.htm';
			for ($gceid = 0; $gceid <= 7; $gceid++) {
				$data['led'.($gceid+1)] =1;
			}
		}
		else if ( $this->getLogicalId() == 'all_off' )
		{
			$url .= 'preset.htm';
			for ($gceid = 0; $gceid <= 7; $gceid++) {
				$data['led'.($gceid+1)] =0;
			}
		}
		else
			return false;
		log::add('ipx800v2','debug','get '.preg_replace("/:[^:]*@/", ":XXXX@", $url).'?'.http_build_query($data));
		$result = @file_get_contents($url.'?'.http_build_query($data));
		$count = 0;
		while ( $result === false )
		{
			$result = @file_get_contents($url.'?'.http_build_query($data));
			if ( $count < 3 ) {
				log::add('ipx800v2','error',__('L\'ipx ne repond pas.',__FILE__)." ".$this->getName()." get ".preg_replace("/:[^:]*@/", ":XXXX@", $url)."?".http_build_query($data));
				throw new Exception(__('L\'ipx ne repond pas.',__FILE__)." ".$this->getName());
			}
			$count ++;
		}
        return false;
    }
}
?>
