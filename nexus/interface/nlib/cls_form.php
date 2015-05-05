<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informação Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em 25/05/2006					*
	*																*
	****************************************************************/

class Form {
	// propriedades de item
	public $itype;
	public $iname;
	public $itemplate;
	public $ivalue;
	public $ivalues;
	public $imaxlength;
	public $ifilter;
	public $ihelp;
	public $ilabel;
	public $ikeysize=0;
	public $iwriteable=0;

	public $sh; // show.hide

	
	
	// variaveis internas
	private $item;
	private $itemcount=-1;
	
	// propriedades do form
	public $action;
	public $name;
	public $type="form";
	

	
	/*
	public $group;
	public $openedroup 		= false;// se um grupo está aberto
	public $groups 			= array();	// grupos abertos 
	public $countgroup 		= 0;	// contator para grupos
	public $arraygroup		= array(); // array dos campos para esconder
	public $arraygroupsep	= array(); // array dos campos separadores para esconder
	public $hidegroup		= array();
	*/
	public $javascript 		= '';
	
	function __construct ($action="") {
		$this->name = $action;
		$this->action=$action;
	}
	
	/***** 
	 * HELPER: fromnow
	 * retorna o unixtime correspondente ao valor recebido + now
	 * nao precisa instanciar a classe
	 */
	function fromnow ($txt,$list) {
		if (trim($txt)=="") { return; }
		
		switch ($list) {
			case "minutes":
				$sum = $txt * 60;
				break;
			case "hours":
				$sum = $txt * 60 * 60;
				break;
			case "days":
				$sum = $txt * 60 * 60 * 24;
				break;
			case "weeks":
				$sum = $txt * 60 * 60 * 24 * 7;
				break;
			case "months":
				$sum = $txt * 60 * 60 * 24 * 30;
				break;
			case "years":
				$sum = $txt * 60 * 60 * 24 * 365;
				break;
		}
		return time() + $sum;
	}
	
	function nextitem () {
		
		$this->itemcount++;
		// tipos possiveis:
		// textbox|passbox|label|multilist|list|textlist

		$this->item[$this->itemcount][help]=str_replace("\n","<BR>",$this->ihelp);
		$this->item[$this->itemcount][label]=$this->ilabel;
		

		$formname = $this->name;
		$itemcount = $this->itemcount;

		$name=$formname."_".$this->iname;
		
		$this->item[$this->itemcount][name]=$name;
		$this->item[$this->itemcount][type]=$this->itype;

		switch ($this->itype) {
			case "hidden":
				
				$value=$this->ivalue;
				$html = "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
				$this->item[$this->itemcount][html]=$html;
				$this->item[$this->itemcount][noshow]=1;
				
				break;
			case "textbox":
				ob_start();
				$value=$this->ivalue;
				$filter=$this->ifilter;
				include DIRHTML."html_form_textbox.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;
				
			case "textarea":
				ob_start();
				$value=$this->ivalue;
				$filter=$this->ifilter;
				include DIRHTML."html_form_textarea.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;
			case "filebox":
				ob_start();
				$value=$this->ivalue;
				$filter=$this->ifilter;
				include DIRHTML."html_form_filebox.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;
			case "list":
				ob_start();
				$value=$this->ivalue;
				$values=$this->ivalues;
				include DIRHTML."html_form_list.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();

				//$this->javascript .= "document.getElementById('$name').onchange();\n";

				break;
			case "select":
				ob_start();
				$value=$this->ivalue;
				$values=$this->ivalues;
				$select=1;
				include DIRHTML."html_form_select.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();

				break;
			case "multilist":
				ob_start();
				$value=$this->ivalue;
				$values=$this->ivalues;
				$avalue=explode(";",$value);
				
				// colocando um ; no final (se nao tiver)
				if (trim($avalue[count($avalue)-1])!="") {
					$avalue[count($avalue)]="";
				}
				$value=implode(";",$avalue);
				
				include DIRHTML."html_form_multilist.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;
			
			case "label":
				ob_start();
				$value=$this->ivalue;
				include DIRHTML."html_form_label.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;
			case "datefromnow":
				ob_start();
				$value=$this->ivalue;
				$values=$this->ivalues;
				include DIRHTML."html_form_datefromnow.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();

				break;
			case "keybox":
				ob_start();
				$value=$this->ivalue;
				$keysize=$this->ikeysize;
				$writeable=$this->iwriteable;
				$keyhelp=_("Click to generate a different key");
				include DIRHTML."html_form_keybox.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;
				
			case "time":
			case "date":
				ob_start();
				$value=$this->ivalue;
				include DIRHTML."html_form_datetime.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;
				
			case "week":
				ob_start();
				$value=$this->ivalue;
				include DIRHTML."html_form_week.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;
				
			case "allowdenytext":
				ob_start();
				$value=$this->ivalue;
				
				$allowtext = $this->ilabel_allow;
				$denytext = $this->ilabel_deny;
				
				include DIRHTML."html_form_allowdenytext.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();
				break;

			case "network":
				ob_start();
				$value=explode("/",$this->ivalue);
				$masks = networksetup::getnetmasks();
				include DIRHTML."html_form_network.php";
				$this->item[$this->itemcount][html]=ob_get_contents();
				ob_end_clean();

				//$this->javascript .= "document.getElementById('$name').onchange();\n";

				break;
				
		}
		
		# Verifica se o grupo está aberto. Se estiver, armazena o INDICE do tr
		if($this->openedGroup){
			array_push($this->arraygroup, "'tr_form_" . $this->iname . "'");
			array_push($this->arraygroupsep, "'tr_sep_form_" . $this->iname . "'");
			$this->countgroup++;
		}
		
		unset($this->itype);
		unset($this->iname);
		unset($this->imaxlength);
		unset($this->itemplate);
		unset($this->ivalue);
		unset($this->ivalues);
		unset($this->ifilter);
		unset($this->ihelp);
		unset($this->ilabel);
		unset($this->sh);
	}
	
	public function draw() {
		$item = $this->item;
		$name = $this->name;
		$action= $this->action;
		$javascript = $this->javascript;
		if(!empty($this->hidegroup)){
			
			for($i = 0; $i < count($this->hidegroup); $i++){
				
				$this->item[$this->itemcount][html] .= "<script>hideTr" . $this->group . "(" . $this->hidegroup[$i] . ")</script>";
				
			}
			
		}
		include DIRHTML."html_form.php";
	}
	
	
	function opengroup ($group) {
		/*
		$this->group = $group;
		// adiciona o grupo ao array
		array_push($this->groups, $this->group);
		$this->openedGroup = true;
		//$this->item[$this->itemcount][html].="\n<div name=\"group".$this->group."\" id=\"group".$this->group."\" style=\"position:absolute;\">\n";
		*/
		
		$this->group = $group;
		$this->openedGroup = true;
		
	}
	
	function closegroup () {
		
		/************************
		*		JAVASCRIPT		*
		************************/
		$js = new javascript;
		/*
		$jsf->arraylines 	= $this->linegroup;
		$jsf->group 		= $this->group;
		$jsf->name 			= $this->name;
		$this->item[$this->itemcount][html] .= $jsf->javascriptfunction("hideTr", 1);
		unset($jsf);
		
		$this->openedGroup 	 = false; 	// fecha informações sobreo grupo
		$this->countgroup 	 = 0; 			// zera o contador
		//$this->item[$this->itemcount][html].="\n</div>\n"; 	// fecha o div do grupo
		$this->linegroup 	 = array();
		*/
		$js->group = $this->group;
		$js->tr_field = $this->arraygroup;
		$js->tr_field_sep = $this->arraygroupsep;
		$js_group = $js->javascriptfunction("hidetr");
		
		$this->item[$this->itemcount][html] .= $js_group[0];
		$this->item[$this->itemcount][html] .= $js_group[1];
		
		# fecha as propriedades do grupo
		$this->openedroup = false;
		$this->countgroup = 0;
		$this->arraygroup = array();
		$this->arraygroupsep = array();
		
	}
	/*
	function add($txt){
		
		$this->item[$this->itemcount][html].=$txt;
		
	}
	*/
	
}

?>
