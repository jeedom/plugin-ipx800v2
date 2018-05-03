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

class ipx800v2_compteur extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
	public function postInsert()
	{
        $nbimpulsion = $this->getCmd(null, 'nbimpulsion');
        if ( ! is_object($nbimpulsion) ) {
            $nbimpulsion = new ipx800v2_compteurCmd();
			$nbimpulsion->setName('Nombre d impulsion');
			$nbimpulsion->setEqLogic_id($this->getId());
			$nbimpulsion->setType('info');
			$nbimpulsion->setSubType('numeric');
			$nbimpulsion->setLogicalId('nbimpulsion');
			$nbimpulsion->setEventOnly(1);
			$nbimpulsion->setDisplay('generic_type','GENERIC_INFO');
			$nbimpulsion->save();
		}
        $nbimpulsionminute = $this->getCmd(null, 'nbimpulsionminute');
        if ( ! is_object($nbimpulsionminute) ) {
            $nbimpulsionminute = new ipx800v2_compteurCmd();
			$nbimpulsionminute->setName('Nombre d impulsion par minute');
			$nbimpulsionminute->setEqLogic_id($this->getId());
			$nbimpulsionminute->setType('info');
			$nbimpulsionminute->setSubType('numeric');
			$nbimpulsionminute->setLogicalId('nbimpulsionminute');
			$nbimpulsionminute->setUnite("Imp/min");
			$nbimpulsionminute->setEventOnly(1);
			$nbimpulsionminute->setConfiguration('calcul', '#brut#');
			$nbimpulsionminute->setDisplay('generic_type','GENERIC_INFO');
			$nbimpulsionminute->save();
		}
	}

	public function postUpdate()
	{
        $nbimpulsion = $this->getCmd(null, 'nbimpulsion');
        if ( ! is_object($nbimpulsion) ) {
            $nbimpulsion = new ipx800v2_compteurCmd();
			$nbimpulsion->setName('Nombre d impulsion');
			$nbimpulsion->setEqLogic_id($this->getId());
			$nbimpulsion->setType('info');
			$nbimpulsion->setSubType('numeric');
			$nbimpulsion->setLogicalId('nbimpulsion');
			$nbimpulsion->setEventOnly(1);
			$nbimpulsion->setDisplay('generic_type','GENERIC_INFO');
			$nbimpulsion->save();
		}
		else
		{
			if ( $nbimpulsion->getDisplay('generic_type') == "" )
			{
				$nbimpulsion->setDisplay('generic_type','GENERIC_INFO');
				$nbimpulsion->save();
			}
		}
        $nbimpulsionminute = $this->getCmd(null, 'nbimpulsionminute');
        if ( ! is_object($nbimpulsionminute) ) {
            $nbimpulsionminute = new ipx800v2_compteurCmd();
			$nbimpulsionminute->setName('Nombre d impulsion par minute');
			$nbimpulsionminute->setEqLogic_id($this->getId());
			$nbimpulsionminute->setType('info');
			$nbimpulsionminute->setSubType('numeric');
			$nbimpulsionminute->setLogicalId('nbimpulsionminute');
			$nbimpulsionminute->setUnite("Imp/min");
			$nbimpulsionminute->setConfiguration('calcul', '#brut#');
			$nbimpulsionminute->setEventOnly(1);
			$nbimpulsionminute->setDisplay('generic_type','GENERIC_INFO');
			$nbimpulsionminute->save();
		}
		else
		{
			if ( $nbimpulsionminute->getDisplay('generic_type') == "" )
			{
				$nbimpulsionminute->setDisplay('generic_type','GENERIC_INFO');
				$nbimpulsionminute->save();
			}
		}
	}

	public function preInsert()
	{
		$gceid = substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2);
		$this->setEqType_name('ipx800v2_compteur');
		$this->setIsEnable(0);
		$this->setIsVisible(0);
	}

    public static function event() {
        $cmd = ipx800v2_compteurCmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
        }
		if ($cmd->execCmd() != $cmd->formatValue(init('nbimpulsion'))) {
			$cmd->setCollectDate('');
			$cmd->event(init('nbimpulsion'));
		}
    }

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=ipx800v2&m=ipx800v2&id=' . $this->getId();
    }
    /*     * **********************Getteur Setteur*************************** */
}

class ipx800v2_compteurCmd extends cmd 
{
    public function preSave() {
        if ( $this->getLogicalId() == 'nbimpulsionminute' ) {
            $calcul = $this->getConfiguration('calcul');
            if ( ! preg_match("/#brut#/", $calcul) ) {
				throw new Exception(__('La formule doit contenir une référecence à #brut#.',__FILE__));
			}
        }
    }

    public function event($_value, $_datetime = NULL, $_loop = 1) {
        if ($this->getLogicalId() == 'nbimpulsionminute') {
			try {
				$calcul = $this->getConfiguration('calcul');
				$calcul = preg_replace("/#brut#/", $_value, $calcul);
				$calcul = scenarioExpression::setTags($calcul);
				$result = evaluate($calcul);
				parent::event($result, $_datetime, $_loop);
			} catch (Exception $e) {
				$EqLogic = $this->getEqLogic();
				log::add('ipx800v2', 'error', $EqLogic->getName()." error in ".$this->getConfiguration('calcul')." : ".$e->getMessage());
				return scenarioExpression::setTags(str_replace('"', '', cmd::cmdToValue($this->getConfiguration('calcul'))));
			}
		} else {
			parent::event($_value, $_datetime, $_loop);
		}
    }
}
?>
