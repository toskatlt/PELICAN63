;(function($){
/**
 * jqGrid Bulgarian Translation 
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = {};

$.jgrid.defaults = {
	recordtext: "�����(�)",
	loadtext: "��������...",
	pgtext : "��"
}
$.jgrid.search = {
    caption: "�������...",
    Find: "������",
    Reset: "�������",
    odata : ['�����', '��������', '��-�����', '��-����� ���=','��-������','��-������ ��� =', '������� �','�������� �','�������' ]
};
$.jgrid.edit = {
    addCaption: "��� �����",
    editCaption: "�������� �����",
    bSubmit: "������",
    bCancel: "�����",
	bClose: "�������",
    processData: "���������...",
    msg: {
        required:"������ � ������������",
        number:"�������� ������� �����!",
        minValue:"���������� ������ �� � ��-������ ��� ����� ��",
        maxValue:"���������� ������ �� � ��-����� ��� ����� ��",
        email: "�� � ������� e-mail �����",
        integer: "�������� ������� ���� �����",
		date: "�������� ������� ����"
    }
};
$.jgrid.del = {
    caption: "���������",
    msg: "�� ������ �� �������� �����?",
    bSubmit: "������",
    bCancel: "�����",
    processData: "���������..."
};
$.jgrid.nav = {
	edittext: " ",
    edittitle: "�������� �� ������ �����",
	addtext:" ",
    addtitle: "�������� �� ��� �����",
    deltext: " ",
    deltitle: "��������� �� ������ �����",
    searchtext: " ",
    searchtitle: "������� �����(�) ",
    refreshtext: "",
    refreshtitle: "������ �������",
    alertcap: "��������������",
    alerttext: "����, �������� �����"
};
// set column module
$.jgrid.col ={
    caption: "������",
    bSubmit: "�����",
    bCancel: "�����"	
};
$.jgrid.errors = {
	errcap : "������",
	nourl : "���� ������� URL �����",
	norecords: "���� ����� �� ���������",
	model : "������� �� ����������� �� �������!"	
};
$.jgrid.formatter = {
	integer : {thousandsSeparator: " ", defaulValue: 0},
	number : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, defaultValue: 0},
	currency : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, prefix: "", suffix:" ??.", defaultValue: 0},
	date : {
		dayNames:   [
			"���", "���", "��", "��", "���", "���", "���",
			"������", "����������", "�������", "�����", "���������", "�����", "������"
		],
		monthNames: [
			"��", "���", "����", "���", "���", "���", "���", "���", "���", "���", "����", "���",
			"������", "��������", "����", "�����", "���", "���", "���", "������", "���������", "��������", "�������", "��������"
		],
		AmPm : ["","","",""],
		S: function (j) {
			if(j==7 || j==8 || j== 27 || j== 28) {
				return '��';
			}
			return ['��', '��', '��'][Math.min((j - 1) % 10, 2)];
		},
		srcformat: 'Y-m-d',
		newformat: 'd/m/Y',
		masks : {
	        ISO8601Long:"Y-m-d H:i:s",
	        ISO8601Short:"Y-m-d",
	        ShortDate: "n/j/Y",
	        LongDate: "l, F d, Y",
	        FullDateTime: "l, F d, Y g:i:s A",
	        MonthDay: "F d",
	        ShortTime: "g:i A",
	        LongTime: "g:i:s A",
	        SortableDateTime: "Y-m-d\\TH:i:s",
	        UniversalSortableDateTime: "Y-m-d H:i:sO",
	        YearMonth: "F, Y"
	    },
	    reformatAfterEdit : false
	},
	baseLinkUrl: '',
	showAction: 'show'
};
})(jQuery);