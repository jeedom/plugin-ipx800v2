$("#table_cmd_ipx800v2_analogique").delegate(".listEquipementInfo", 'click', function () {
	var el = $(this);
	jeedom.cmd.getSelectModal({ cmd: { type: 'info' } }, function (result) {
		var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
		calcul.atCaret('insert', result.human);
	});
});

$("#table_cmd_ipx800v2_analogique").delegate(".Formule", 'change', function () {
	$('.choixFormule option[value=""]').prop('selected', true);
});


$("#table_cmd_ipx800v2_analogique").delegate(".choixFormule", 'change', function () {
	switch ($(this).find('option').filter(":selected").value()) {
		case 'LM35Z':
			formule = '#brut# * 0.323';
			unite = '°C';
			break;
		case 'T4012':
			formule = '#brut# * 0.323 - 50';
			unite = '°C';
			break;
		case 'Voltage':
			formule = '#brut# * 0.00323';
			unite = 'V';
			break;
		case 'SHT-X3L':
			formule = '#brut# * 0.09775';
			unite = '% Lum';
			break;
		case 'SHT-X3T':
			formule = '( #brut# * 0.00323 - 1.63 ) / 0.0326';
			unite = '°C';
			break;
		case 'SHT-X3H':
			formule = '( ( #brut# * 0.00323 / 3.3 ) - 0.1515 ) / 0.00636 / 1.0546';
			unite = '% RH';
			break;
		case 'TC100':
			formule = '( ( #brut# * 0.00323 ) - 0.25 ) / 0.028';
			unite = '°C';
			break;
		case 'X400 CT10A':
			formule = '#brut# / 0.00323';
			unite = 'A';
			break;
		case 'CT20A':
			formule = '#brut# / 0.00646';
			unite = 'A';
			break;
		case 'Ph':
			formule = '#brut#';
			unite = 'Ph';
			break;
		case 'SHT-X3HC':
			formule = '( ( #brut# * 0.00323 / 3.3 ) - 0.1515 ) / 0.00636 / 1.0546 / ( 1.0546 - ( 0.00216 * #temp# ) )';
			formule = '( ( #brut# * 0.00323 / 3.3 ) - 0.1515 ) / 0.00636 / (1.0546 - (0.00216 * #temp#))';
			unite = '% RH';
			alert('Remplacer #temp# par la température réél.');
			break;
		default:
			formule = '#brut#';
			unite = '';
	}
	$('.cmdAttr[data-l2key=calcul]').val(formule);
	$('.cmdAttr[data-l1key=unite]').val(unite);
});

function addCmdToTable(_cmd) {
	if (!isset(_cmd)) {
		var _cmd = { configuration: {} };
	}
	if (!isset(_cmd.configuration)) {
		_cmd.configuration = {};
	}

	if (init(_cmd.type) == 'info') {
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" >';
		if (init(_cmd.logicalId) == 'brut') {
			tr += '<input type="hiden" name="brutid" value="' + init(_cmd.id) + '">';
		}
		tr += '<td>';
		tr += '<span class="cmdAttr" data-l1key="id"></span>';
		tr += '</td>';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}"></td>';
		tr += '<td class="expertModeVisible">';
		tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
		tr += '</td>';
		tr += '<td>';
		if (init(_cmd.logicalId) == 'nbimpulsionminute') {
			tr += '<textarea class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="calcul" style="height : 33px;" placeholder="{{Calcul}}"></textarea> (utiliser #brut# dans la formule)';
		}
		if (init(_cmd.logicalId) == 'reel') {
			tr += '<textarea class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="calcul" style="height : 33px;" placeholder="{{Calcul}}"></textarea>';
			tr += '<a class="btn btn-default cursor listEquipementInfo" data-input="calcul" style="margin-top : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>';
			tr += '<select class="cmdAttr form-control tooltips input-sm choixFormule" style="margin-top : 5px;" title="{{Formule standard}}" data-l1key="configuration" data-l2key="type">';
			tr += '<option value=""></option>';
			tr += '<option value="LM35Z">Sonde LM35Z</option>';
			tr += '<option value="T4012">Sonde T4012</option>';
			tr += '<option value="Voltage">Voltage</option>';
			tr += '<option value="SHT-X3L">SHT-X3:Light-LS100</option>';
			tr += '<option value="SHT-X3T">SHT-X3:Temp-LS100</option>';
			tr += '<option value="SHT-X3H">SHT-X3:RH-SH100</option>';
			tr += '<option value="SHT-X3HC">SHT-X3:RH-SH100 compensé</option>';
			tr += '<option value="TC100">TC 100</option>';
			tr += '<option value="CT10A">X400 CT10A</option>';
			tr += '<option value="CT20A">X400 CT20A</option>';
			tr += '<option value="Ph">X200 pH Probe</option>';
			tr += '<option value="Autre">Autre</option>';
			tr += '</select>';
		}
		tr += '</td>';
		tr += '<td>';
		if (init(_cmd.logicalId) == 'reel' || init(_cmd.logicalId) == 'nbimpulsionminute') {
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" style="width : 90px;" placeholder="{{Unite}}">';
		} else {
			tr += '<input type=hidden class="cmdAttr form-control input-sm" data-l1key="unite" value="">';
		}
		if (init(_cmd.logicalId) == 'reel') {
			tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40%;display : inline-block;"><br>';
			tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40%;display : inline-block;"><br>';
		} else {
			tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40%;display : none;"> ';
			tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40%;display : none;">';
		}
		tr += '</td>';
		tr += '<td>';
		tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized"/> {{Historiser}}<br/></span>';
		tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
		if (init(_cmd.subType) == 'binary') {
			tr += '<span class="expertModeVisible"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary" /> {{Inverser}}<br/></span>';
		}
		if (init(_cmd.logicalId) == 'reel') {
			tr += '<span class="expertModeVisible"><input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="minValueReplace" value="1"/> {{Correction Min	 Auto}}<br>';
			tr += '<input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="maxValueReplace" value="1"/> {{Correction Max Auto}}<br></span>';
		} tr += '</td>';
		tr += '</td>';
		tr += '<td>';
		if (is_numeric(_cmd.id)) {
			tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
		}
		tr += '</td>';
		table_cmd = '#table_cmd';
		if ($(table_cmd + '_' + _cmd.eqType).length) {
			table_cmd += '_' + _cmd.eqType;
		}
		$(table_cmd + ' tbody').append(tr);
		$(table_cmd + ' tbody tr:last').setValues(_cmd, '.cmdAttr');
	}
	if (init(_cmd.type) == 'action') {
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
		tr += '<td>';
		tr += '<span class="cmdAttr" data-l1key="id"></span>';
		tr += '</td>';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
		tr += '</td>';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
		tr += '<input class="cmdAttr" data-l1key="configuration" data-l2key="virtualAction" value="1" style="display:none;" >';
		tr += '</td>';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="infoName" placeholder="{{Nom information}}" style="margin-bottom : 5px;width : 50%; display : inline-block;">';
		tr += '<a class="btn btn-default btn-sm cursor listEquipementAction" data-input="infoName" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value" placeholder="{{Valeur}}" style="margin-bottom : 5px;width : 50%; display : inline-block;">';
		tr += '<a class="btn btn-default btn-sm cursor listEquipementInfo" data-input="value" style="margin-left : 5px;"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>';
		tr += '</td>';
		tr += '<td></td>';
		tr += '<td>';
		tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
		tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40%;display : inline-block;"> ';
		tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40%;display : inline-block;">';
		tr += '</td>';
		tr += '<td>';
		if (is_numeric(_cmd.id)) {
			tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
			tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
		}
		tr += '</td>';
		tr += '</tr>';

		table_cmd = '#table_cmd';
		if ($(table_cmd + '_' + _cmd.eqType).length) {
			table_cmd += '_' + _cmd.eqType;
		}
		$(table_cmd + ' tbody').append(tr);
		$(table_cmd + ' tbody tr:last').setValues(_cmd, '.cmdAttr');
		var tr = $(table_cmd + ' tbody tr:last');
		jeedom.eqLogic.builSelectCmd({
			id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
			filter: { type: 'info' },
			error: function (error) {
				$('#div_alert').showAlert({ message: error.message, level: 'danger' });
			},
			success: function (result) {
				tr.find('.cmdAttr[data-l1key=value]').append(result);
				tr.setValues(_cmd, '.cmdAttr');
			}
		});
	}
}

$('#bt_goCarte').on('click', function () {
	$('#md_modal').dialog({ title: "{{Accèder à l'interface de l'IPX}}" });
	window.open('http://' + $('.eqLogicAttr[data-l2key=username]').value() + ':' + $('.eqLogicAttr[data-l2key=password]').value() + '@' + $('.eqLogicAttr[data-l2key=ip]').value() + ':' + $('.eqLogicAttr[data-l2key=port]').value() + '/');
});

$('.eqLogicAction[data-action=hide]').on('click', function () {
	var eqLogic_id = $(this).attr('data-eqLogic_id');
	$('.sub-nav-list').each(function () {
		if ($(this).attr('data-eqLogic_id') == eqLogic_id) {
			$(this).toggle();
		}
	});
	return false;
});

function prePrintEqLogic() {
	$('.eqLogic').hide();
}

$("#table_cmd_ipx800v2_analogique").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });
$("#table_cmd_ipx800v2_relai").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });
$("#table_cmd_ipx800v2_bouton").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });
$("#table_cmd_ipx800v2_compteur").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });
$("#table_cmd").sortable({ axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true });