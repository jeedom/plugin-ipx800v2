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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function ipx800v2_install() {
	$cron = cron::byClassAndFunction('ipx800v2', 'daemon');
	if (!is_object($cron)) {
		$cron = new cron();
		$cron->setClass('ipx800v2');
		$cron->setFunction('daemon');
		$cron->setEnable(1);
		$cron->setDeamon(1);
		$cron->setTimeout(1440);
		$cron->setSchedule('* * * * *');
		$cron->save();
	}
	config::save('temporisation_lecture', 60, 'ipx800v2');
	$cron->start();
}

function ipx800v2_update() {
	$cron = cron::byClassAndFunction('ipx800v2', 'daemon');
	if (!is_object($cron)) {
		$cron = new cron();
		$cron->setClass('ipx800v2');
		$cron->setFunction('daemon');
		$cron->setEnable(1);
		$cron->setDeamon(1);
		$cron->setTimeout(1440);
		$cron->setSchedule('* * * * *');
		$cron->save();
		$cron->stop();
		$cron->start();
	} else {
		ipx800v2::deamon_start();
	}
	foreach (eqLogic::byType('ipx800v2') as $eqLogic) {
		$eqLogic->setConfiguration('type', 'carte');
		$eqLogic->save();
	}
	foreach (eqLogic::byType('ipx800v2_bouton') as $SubeqLogic) {
		$SubeqLogic->setConfiguration('type', 'bouton');
		$SubeqLogic->setEqType_name('ipx800v2');
		$SubeqLogic->save();
	}
	foreach (eqLogic::byType('ipx800v2_analogique') as $SubeqLogic) {
		$SubeqLogic->setConfiguration('type', 'analogique');
		$SubeqLogic->setEqType_name('ipx800v2');
		$SubeqLogic->save();
	}
	foreach (eqLogic::byType('ipx800v2_relai') as $SubeqLogic) {
		$SubeqLogic->setConfiguration('type', 'relai');
		$SubeqLogic->setEqType_name('ipx800v2');
		$SubeqLogic->save();
	}
	foreach (eqLogic::byType('ipx800v2_compteur') as $SubeqLogic) {
		$SubeqLogic->setConfiguration('type', 'compteur');
		$SubeqLogic->setEqType_name('ipx800v2');
		$SubeqLogic->save();
	}
	if (config::byKey('temporisation_lecture', 'ipx800v2') == "") {
		config::save('temporisation_lecture', 60, 'ipx800v2');
	}
	config::remove('subClass', 'ipx800v2');
}

function ipx800v2_remove() {
	config::remove('temporisation_lecture', 'ipx800v2');
	$cron = cron::byClassAndFunction('ipx800v2', 'daemon');
	if (is_object($cron)) {
		$cron->remove();
	}
	config::remove('subClass', 'ipx800v2');
}
