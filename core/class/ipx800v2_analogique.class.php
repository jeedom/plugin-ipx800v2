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

class ipx800v2_analogique extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
	public function preUpdate()
	{
        $brut = $this->getCmd(null, 'voltage');
        if ( is_object($brut) ) {
			$brut->setLogicalId('brut');
			$brut->save();
		} else {
			$brut = $this->getCmd(null, 'brut');
		}
        $brut = $this->getCmd(null, 'brut');
        if ( ! is_object($brut) ) {
            $brut = new ipx800_analogiqueCmd();
			$brut->setName('Brut');
			$brut->setEqLogic_id($this->getId());
			$brut->setType('info');
			$brut->setSubType('numeric');
			$brut->setLogicalId('brut');
			$brut->setIsVisible(0);
			$brut->setEventOnly(1);
			$brut->setDisplay('generic_type','GENERIC_INFO');
			$brut->save();
		}
		else
		{
			if ( $brut->getDisplay('generic_type') == "" )
			{
				$brut->setDisplay('generic_type','GENERIC_INFO');
				$brut->save();
			}
		}
        $reel = $this->getCmd(null, 'reel');
        if ( ! is_object($reel) ) {
            $reel = new ipx800v2_analogiqueCmd();
			$reel->setName('Réel');
			$reel->setEqLogic_id($this->getId());
			$reel->setType('info');
			$reel->setSubType('numeric');
			$reel->setLogicalId('reel');
			$reel->setEventOnly(1);
			$reel->setConfiguration('calcul', '#' . $brut->getId() . '#');
			$reel->setDisplay('generic_type','GENERIC_INFO');
			$reel->save();
		}
		else
		{
			if ( $reel->getConfiguration('type') == "" )
			{
				switch ($reel->getConfiguration('calcul')) {
					case '#brut# * 0.323':
						$reel->setConfiguration('type', 'LM35Z');
						break;
					case '#brut# * 0.323 - 50':
						$reel->setConfiguration('type', 'T4012');
						break;
					case '#brut# * 0.00323':
						$reel->setConfiguration('type', 'Voltage');
						break;
					case '#brut# * 0.09775':
						$reel->setConfiguration('type', 'SHT-X3L');
						break;
					case '( #brut# * 0.00323 - 1.63 ) / 0.0326':
						$reel->setConfiguration('type', 'SHT-X3T');
						break;
					case '( ( #brut# * 0.00323 / 3.3 ) - 0.1515 ) / 0.00636 / 1.0546':
						$reel->setConfiguration('type', 'SHT-X3H');
						break;
					case '( ( #brut# * 0.00323 ) - 0.25 ) / 0.028':
						$reel->setConfiguration('type', 'TC100');
						break;
					case '#brut# * 0.00646':
						$reel->setConfiguration('type', 'CT20A');
						break;
					case '#brut# * 0.01615':
						$reel->setConfiguration('type', 'CT50A');
						break;
					case '#brut# / 100':
						$reel->setConfiguration('type', 'Ph');
						break;
					default:
						if ( preg_match('!\( \( #brut# \* 0.00323 / 3.3 \) - 0.1515 \) / 0.00636 / \( 1.0546 - \( 0.00216 \* .* \) \)!', $reel->getConfiguration('calcul')) )
							$reel->setConfiguration('type', 'SHT-X3HC');
						else
							$reel->setConfiguration('type', 'Autre');
						break;
				}
			}
			switch ($reel->getConfiguration('type')) {
				case 'LM35Z':
					$reel->setDisplay('generic_type','TEMPERATURE');
					break;
				case 'T4012':
					$reel->setDisplay('generic_type','TEMPERATURE');
					break;
				case 'Voltage':
					$reel->setDisplay('generic_type','VOLTAGE');
					break;
				case 'SHT-X3L':
					$reel->setDisplay('generic_type','BRIGHTNESS');
					break;
				case 'SHT-X3T':
					$reel->setTemplate('dashboard', 'thermometre');
					$reel->setTemplate('mobile', 'thermometre');
					$reel->setDisplay('generic_type','TEMPERATURE');
					break;
				case 'SHT-X3H':
					$reel->setDisplay('generic_type','HUMIDITY');
					break;
				case 'TC100':
					$reel->setTemplate('dashboard', 'thermometre');
					$reel->setTemplate('mobile', 'thermometre');
					$reel->setDisplay('generic_type','TEMPERATURE');
					break;
				case 'X400 CT10A':
					$reel->setDisplay('generic_type','CONSUMPTION');
					break;
				case 'CT20A':
					$reel->setDisplay('generic_type','CONSUMPTION');
					break;
				case 'CT50A':
					$reel->setDisplay('generic_type','CONSUMPTION');
					break;
				case 'Ph':
					$reel->setDisplay('generic_type','GENERIC_INFO');
					break;
				case 'SHT-X3HC':
					$reel->setDisplay('generic_type','HUMIDITY');
					break;
				default:
					$reel->setDisplay('generic_type','GENERIC_INFO');
					break;
			}
			$reel->save();
		}
	}
	public function postInsert()
	{
        $brut = $this->getCmd(null, 'brut');
        if ( ! is_object($brut) ) {
            $brut = new ipx800v2_analogiqueCmd();
			$brut->setName('Brut');
			$brut->setEqLogic_id($this->getId());
			$brut->setType('info');
			$brut->setSubType('numeric');
			$brut->setLogicalId('brut');
			$brut->setIsVisible(0);
			$brut->setDisplay('generic_type','GENERIC_INFO');
			$brut->save();
		}
        $reel = $this->getCmd(null, 'reel');
        if ( ! is_object($reel) ) {
            $reel = new ipx800v2_analogiqueCmd();
			$reel->setName('Réel');
			$reel->setEqLogic_id($this->getId());
			$reel->setType('info');
			$reel->setSubType('numeric');
			$reel->setLogicalId('reel');
			$reel->setEventOnly(1);
			$reel->setConfiguration('calcul', '#' . $brut->getId() . '#');
			$reel->setDisplay('generic_type','GENERIC_INFO');
			$reel->save();
		}
	}

	public function preInsert()
	{
		$gceid = substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2);
		$this->setEqType_name('ipx800v2_analogique');
		$this->setIsEnable(0);
		$this->setIsVisible(0);
	}

    public static function event() {
        $cmd = ipx800v2_analogiqueCmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
        }
		if ($cmd->execCmd() != $cmd->formatValue(init('voltage'))) {
			$cmd->setCollectDate('');
			$cmd->event(init('voltage'));
		}
    }

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=ipx800v2&m=ipx800v2&id=' . $this->getId();
    }
    /*     * **********************Getteur Setteur*************************** */
}

class ipx800v2_analogiqueCmd extends cmd 
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
    public function preSave() {
        if ( $this->getLogicalId() == 'reel' ) {
            $this->setValue('');
            $calcul = $this->getConfiguration('calcul');
            preg_match_all("/#([0-9]*)#/", $calcul, $matches);
            $value = '';
            foreach ($matches[1] as $cmd_id) {
                if (is_numeric($cmd_id)) {
                    $cmd = self::byId($cmd_id);
                    if (is_object($cmd) && $cmd->getType() == 'info') {
                        $value .= '#' . $cmd_id . '#';
                        break;
                    }
                }
            }
			$this->setConfiguration('calcul', $calcul);
			
            $this->setValue($value);
        }
    }

    public function execute($_options = null) {
        if ($this->getLogicalId() == 'reel') {
			try {
				$calcul = $this->getConfiguration('calcul');
				if ( preg_match("/#brut#/", $calcul) ) {
					$EqLogic = $this->getEqLogic();
					$brut = $EqLogic->getCmd(null, 'brut');
					$calcul = preg_replace("/#brut#/", "#".$brut->getId()."#", $calcul);
				}
				$calcul = scenarioExpression::setTags($calcul);
				$result = evaluate($calcul);
				if (is_numeric($result)) {
					$result = number_format($result, 2);
				} else {
					$result = str_replace('"', '', $result);
				}
				if ($this->getSubType() == 'numeric') {
					if (strpos($result, '.') !== false) {
						$result = str_replace(',', '', $result);
					} else {
						$result = str_replace(',', '.', $result);
					}
				}
				return $result;
			} catch (Exception $e) {
				$EqLogic = $this->getEqLogic();
				log::add('ipx800v2', 'error', $EqLogic->getName()." error in ".$this->getConfiguration('calcul')." : ".$e->getMessage());
				return scenarioExpression::setTags(str_replace('"', '', cmd::cmdToValue($this->getConfiguration('calcul'))));
			}
		} else {
			return $this->getConfiguration('value');
		}
    }

    public function imperihomeCmd() {
 		if ( $this->getLogicalId() == 'reel' ) {
			return true;
		}
		else {
			return false;
		}
    }
}
?>
