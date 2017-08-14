<?php
/*
	this file is part of SILEX
	SILEX : RIA developement tool - see http://silex-ria.org/

	SILEX is (c) 2004-2007 Alexandre Hoyau and is released under the GPL License:

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License (GPL)
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	
	To read the license please visit http://www.gnu.org/copyleft/gpl.html
*/

// query : http://framework.zend.com/manual/fr/zend.search.lucene.query-language.html
// http://lucene.apache.org/java/2_0_0/queryparsersyntax.html
// when called by amfPhp
set_include_path(get_include_path() . PATH_SEPARATOR . "../../");
set_include_path(get_include_path() . PATH_SEPARATOR . "../library/");

// when called from index.php
set_include_path(get_include_path() . PATH_SEPARATOR . "cgi/library/");

/** Zend_Debug */
require_once 'Zend/Debug.php';
require_once "Zend/Log/Writer/Stream.php";
/** Zend_Lucene */
require_once 'Zend/Search/Lucene.php';
require_once("rootdir.php");
require_once("config_editor.php");


class silex_search{
	var $logger=null;
	// field types arrays
	var $Text=null;
	var $HtmlText=null;
	var $Keyword=null;
	var $UnIndexed=null;
	var $UnStored=null;
	var $lastQuery=null;
	var $sepCharForDeeplinks=".";
	
	// constants
	//const sepchar=".";
	
	const serverRootRelativePath="../../";
	const searchIndexFolderName="search_index";
	const seoDataFilesExtension=".seodata.xml";
	
	const componentClassFolder="cgi/services/";
	
	// main tags (org.silex.core.Application::getSeoData)
	const seoTitleSectionTag="title";
	const seoLinkSectionTag="link";
	const seoPubDateSectionTag="pubDate";
	const seoContentSectionTag="content";
	const seoUrlBaseSectionTag="urlBase";
	const seoDeeplinkPlayerTag="deeplink";
	const seoExactDeeplinkPlayerTag="exactDeeplink";

	// players tags(ui.players.UiBase::getSeoData)
	const seoTextPlayerTag="text";
	const seoTagsPlayerTag="tags";
	const seoDescriptionPlayerTag="description";
	const seoLinksPlayerTag="links";
	const seoHtmlEquivalentPlayerTag="htmlEquivalent";
	const seoContextPlayerTag="context";
	
	// components with server side cgi
	const seoClassNameComponentTag="className";
	// component params
	const seoComponentParamsComponentTag="componentParams";
	const seoFormNameComponentTag="formName";
	const seoSelectedFieldNamesComponentTag="selectedFieldNames";
	const seoWhereClauseComponentTag="whereClause";
	const seoOrderByComponentTag="orderBy";
	const seoLimitComponentTag="limit";
	const seoOffsetComponentTag="offset";
	const seoIdFieldComponentTag="idField";
	const seoResultContainerComponentTag="dataContainer";
	const seoTemplateComponentTag="template";
	const seoDeeplinkFormatComponentTag="deeplinkFormat";
	
	function silex_search($sepCharForDeeplinks=null){
		$this->logger = new logger("silex_search");
	
		if ($sepCharForDeeplinks!=null)
			$this->sepCharForDeeplinks=$sepCharForDeeplinks;
		
		$this->logger->debug("sepCharForDeeplinks : " . $this->sepCharForDeeplinks);
		// init zend lucene
		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');

		// field types array init
		$this->Text=Array();
		$this->HtmlText=Array();
		$this->Keyword=Array();
		$this->UnIndexed=Array();
		$this->UnStored=Array();

		// init fields
		/*
		$silex_search_obj->addField(link_field);
		$silex_search_obj->addField(deeplink_title_field);
		$silex_search_obj->addField(deeplink_description_field);
		$silex_search_obj->addField(deeplink_keywords_field,"Keyword");
		$silex_search_obj->addField(deeplink_href_field,"UnIndexed");
		$silex_search_obj->addField(deeplink_htmlEquivalent_field);
		*/
		$this->addField(self::seoTextPlayerTag);
		$this->addField(self::seoTagsPlayerTag,"Keyword");
		$this->addField(self::seoDescriptionPlayerTag);
		$this->addField(self::seoDeeplinkPlayerTag);//,"Keyword");
		$this->addField(self::seoExactDeeplinkPlayerTag,"Keyword");//,"Keyword");
		$this->addField(self::seoLinksPlayerTag,"UnIndexed");
		$this->addField(self::seoHtmlEquivalentPlayerTag,"UnIndexed");
		$this->addField(self::seoContextPlayerTag);

		$this->addField(self::seoTitleSectionTag);
		$this->addField(self::seoLinkSectionTag);
		$this->addField(self::seoUrlBaseSectionTag,"UnIndexed");
		$this->addField(self::seoPubDateSectionTag,"UnIndexed");
		
		$this->addField(self::seoClassNameComponentTag,"UnIndexed");
		$this->addField(self::seoComponentParamsComponentTag,"UnIndexed");
	}

	function find($websiteContentFolderPath,$search,$limit=0){
		$indexFolder=$websiteContentFolderPath.self::searchIndexFolderName;
		if ($this->logger) $this->logger->debug("silex_search.php find($websiteContentFolderPath,$search) -> ".$indexFolder);
		if (is_dir($indexFolder) /*&& is_file($indexFolder."/segments")*/){
			$index = new Zend_Search_Lucene($indexFolder);

			//$this->logger->debug("silex_search.php find 		Index contains ".$index->count()." documents.");
			
			// replace ',' by spaces : coma separated list becomes space separated list
			$search=str_replace(","," ",$search);

			Zend_Search_Lucene::setResultSetLimit($limit);
			$this->lastQuery = Zend_Search_Lucene_Search_QueryParser::parse($search);
			$hits = $index->find($this->lastQuery);
			if ($this->logger) $this->logger->debug("silex_search.php find		Search for ".$search." returned " .count($hits). " hits on an index of ".$index->count()." documents.");

			return $hits;
		}
		if ($this->logger) $this->logger->info("silex_search.php find $indexFolder/segments is not a file - index may not exist yet");
		// the index does not exist
		return null;
	}
	// see field types : http://framework.zend.com/manual/fr/zend.search.lucene.html#zend.search.lucene.index-creation.understanding-field-types
	function addField($fieldName,$fieldType="Text"){
		if ($this->logger) $this->logger->debug("silex_search.php addField($fieldName,$fieldType)");
		array_push($this->$fieldType,$fieldName);
		//$this->logger->debug(Zend_Debug::dump($this->$fieldType,null,false));
	}
	function removeField($fieldName,$fieldType){
		if ($this->logger) $this->logger->debug("silex_search.php removeField($fieldName,$fieldType)");
		foreach ($this->$fieldType as $name){
			if ($name == $fieldName){
				unset ($this->$fieldType[$name]);
				return true;
			}
		}
		return false;
	}
	function removeAllFields($fieldType){
		if ($this->logger) $this->logger->debug("silex_search.php removeAllFields($fieldType)");
		$this->$fieldType=Array();
		return true;
	}
	function storeSeoData($websiteContentFolderPath,$sectionName,$seoObject){
		if ($this->logger) $this->logger->debug("silex_search.php storeSeoData($websiteContentFolderPath,$sectionName,".print_r($seoObject,true).") ");
		// **
		// WITH XML
		// store html data in xml file
		// build seo file name
		$seoFileName=$websiteContentFolderPath.$sectionName.self::seoDataFilesExtension;
		if ($this->logger) $this->logger->debug("silex_search.php storeSeoData open file: $seoFileName");

		// open seo file
		$xmlFile=fopen($seoFileName,"w");
		if (!$xmlFile) return "error opening file ".$seoFileName." - Section data was saved anyway";

		// build xml data
		$seoData_xml=arrayToXML($seoObject);

		// write xml data
		if ($seoData_xml){
			// add UTF-8 header
			$seoData_xml="\xEF\xBB\xBF".$seoData_xml; 
			if (!fputs($xmlFile,$seoData_xml)) return "error writing to file ".$seoFileName." - Section data was saved anyway";
		}
		// close seo file
		fclose($xmlFile);

		// generate the index
//		if ($this->silex_server_ini["AUTOMATIC_INDEX_CREATION_ON_SAVE"]=="true")
//			$this->createIndex($websiteContentFolderPath,$startSectionName);
		return "";
	}
///////////////////////////		
	function createIndex($websiteContentFolderPath,$firstSectionName){
		if ($this->logger) $this->logger->debug("silex_search.php createIndex($websiteContentFolderPath,$firstSectionName) ");

		// let php run for 5 minuts if possible (no effect when PHP is running in safe mode)
		@set_time_limit(300);
		

		// to do : check inputs (array length at least)
		
		// **
		// retrieve config data for accessors (silex.config.*)
		$dataContainer_array=Array();
		
		$server_config = new server_config(); 
		$config_editor = new config_editor($this->logger);
		// from server ini files
		// from silex_server.ini
		foreach ($server_config->silex_server_ini as $key => $value) {
			$dataContainer_array["silex.config.".$key]=$value;
		}
		// from silex.ini
		foreach ($server_config->silex_client_ini as $key => $value) {
			$dataContainer_array["silex.config.".$key]=$value;
		}
		// from website conf file
		print_r($server_config);
		$filePath=$websiteContentFolderPath . $server_config->silex_server_ini["WEBSITE_CONF_FILE"];
		$this->logger->debug("filepath : $filePath");
		$temp_array = $config_editor->readConfigFile($filePath, 'flashvars');
		foreach ($temp_array as $key => $value) {
			$dataContainer_array["silex.config.".$key]=$value;
		}
		
		//create the index
		$index = new Zend_Search_Lucene(ROOTPATH . $websiteContentFolderPath.self::searchIndexFolderName, true);
		
		// index files
		$index=$this->indexFilesRecursively($websiteContentFolderPath,$firstSectionName,$index,$dataContainer_array);
		
		// submit changes
		$index->commit();
		if ($this->logger) $this->logger->debug("---------------------------------------------------\nsilex_search.php createIndex		Index contains ".$index->count()." documents.</b>");
		return $index->count();
	}
	/* indexFilesRecursively
	 * indexes in $index all sections starting from $sectionName
	 *@param $dataContainer_array	array of objects to be used in accessors (eg dataContainer.website index used for <<dataContainer.website.id>>
	 *@param $deeplink			forces to concidere this as a deeplink for the section instead of the ones in xml (link tag of each item)
	 */
	function indexFilesRecursively($websiteContentFolderPath,$sectionName,$index,$dataContainer_array,$deeplink=null){
		if ($this->logger) $this->logger->debug("silex_search.php indexFilesRecursively($websiteContentFolderPath,$sectionName,".$index->count().") ");
		// build file name
		$seoDataFileName=ROOTPATH . $websiteContentFolderPath . $sectionName . self::seoDataFilesExtension;
		$this->logger->debug("silex_search.php indexFilesRecursively Indexing file : $seoDataFileName");
		if (is_file($seoDataFileName)){
			
			// extract xml data from file
			//$xml_str=utf8_decode (file_get_contents($seoDataFileName));
			//$xml_str=iconv("UTF-8", "ISO-8859-1", file_get_contents($seoDataFileName));
			$xml_str=file_get_contents($seoDataFileName);
			// remove bom
			$xml_str=substr($xml_str,3);
			
			//$this->logger->debug("seo data loaded : ". $xml_str);
			if ($xml_str==false){
				if ($this->logger) $this->logger->emerg("silex_search.php indexFilesRecursively	ERROR opening file $fileName");
			}
			else{

				$this->logger->debug("silex_search.php indexFilesRecursively Adding ".$seoDataFileName." to the index... ");//.$xml_str);
				$item=XML_to_array($xml_str);
				//if ($this->logger) $this->logger->debug("silex_search.php indexFilesRecursively ".$item);
				if (!$item){
					if ($this->logger) $this->logger->emerg("silex_search.php indexFilesRecursively	ERROR converting xml data of file $fileName");
				}
				else{
					if ($item && $item[self::seoContentSectionTag]){
						// index each element (player)
						$this->logger->debug("silex_search.php indexFilesRecursively - index section content...");	
						foreach ($item[self::seoContentSectionTag] as $player) {
							$this->logger->debug("silex_search.php indexFilesRecursively - COMPONENT OR PLAYER? ".self::seoClassNameComponentTag." - ".$player[self::seoClassNameComponentTag]." - ".self::componentClassFolder." - ".is_file(ROOTPATH . self::componentClassFolder.$player[self::seoClassNameComponentTag].".php"));	
							// if the element is a component and has server side counterpart, let's get the links and seo data from server side script
							if ($player[self::seoClassNameComponentTag] && is_file(ROOTPATH . self::componentClassFolder.$player[self::seoClassNameComponentTag].".php")===TRUE){
								if ($this->logger) $this->logger->debug("silex_search.php indexFilesRecursively - ".$player["title"]." is a COMPONENT");	
								$index=$this->indexComponent($websiteContentFolderPath,$item,$player,$index,$dataContainer_array,$deeplink);
							}
							else{
								if ($this->logger) $this->logger->debug("silex_search.php indexFilesRecursively - ".$player["title"]." is a PLAYER");	
								$index=$this->indexPlayer($websiteContentFolderPath,$item,$player,$index,$dataContainer_array,$deeplink);
							}
						}
					}
				}
			}
		}else{
			$this->logger->alert("seofile not found : " . $seoDataFileName);
		}
		return $index;
	}
	/* indexPlayer
	 * indexes a new document in $index with
	 * - the data of $player
	 * - the section data in $item (<=> xml data)
	 * call recursively indexFilesRecursively function for all links of the player
	 */
	function indexPlayer($websiteContentFolderPath,$item,$player,$index,$dataContainer_array,$deeplink=null){
		if ($this->logger) $this->logger->debug("silex_search.php indexPlayer($websiteContentFolderPath,$player,index,$deeplink) ");

		// create a new record
		$doc = new Zend_Search_Lucene_Document();
		
		// adds the section tags to each player tags
		if ($this->logger) $this->logger->debug("silex_search.php indexPlayer			adds the section tags to each player tags to the index...");
		$deeplinkFound=false;
		foreach($item as $tag => $value){
			// for all tags except <content> tag
			if ($tag!=self::seoContentSectionTag){
				if ($tag==self::seoDeeplinkPlayerTag){
					if ($deeplink!=null){
						// forces this page deeplink
						$exactDeeplink=$this->cleanId($deeplink);
						$this->addTagToDoc($doc,$tag,$exactDeeplink."/");
						$deeplinkFound=true;
					}
					else{
						// use the player deeplink tag
						$exactDeeplink=$this->cleanId($this->revealAccessors($value,$dataContainer_array));
						$this->addTagToDoc($doc,$tag,$exactDeeplink."/");
					}
				}
				else{
					// use the link tag as a deeplink for this page
					$this->addTagToDoc($doc,$tag,$this->revealAccessors($value,$dataContainer_array));
				}
			}
		}
		if ($deeplinkFound==false && $deeplink!=null){
			$exactDeeplink=$this->cleanId($deeplink);
			$this->addTagToDoc($doc,self::seoDeeplinkPlayerTag,$exactDeeplink."/");
		}
		
		// add the player's tags to the document
		if ($this->logger) $this->logger->debug("silex_search.php createIndex			add the player's tags to the document...");
		foreach ($player as $tag => $value) {
			if ($tag==self::seoLinksPlayerTag){
				if ($this->logger) $this->logger->debug("silex_search.php createIndex found links: ".count($value));
				$links="";
				foreach ($value as $linkObject) {
					// call recursively indexFilesRecursively BUT do not propagate the force deeplink, use the deeplink in the <link> tag for all subsequent pages 
					$index=$this->indexFilesRecursively($websiteContentFolderPath,$linkObject["link"],$index,$dataContainer_array);
					$links.="<a context='".$linkObject["context"]."' href='".$this->revealAccessors($linkObject["link"],$dataContainer_array)."/'>".$this->revealAccessors($linkObject["title"],$dataContainer_array)."</a>";
				}
				//$this->logger->debug("silex_search.php createIndex add found links: $links");
				if ($links!="")
					$this->addTagToDoc($doc,$tag,$this->revealAccessors($links,$dataContainer_array));
			}
			else
				$this->addTagToDoc($doc,$tag,$this->revealAccessors($value,$dataContainer_array));
		}
		if ($exactDeeplink){
			$this->addTagToDoc($doc,self::seoExactDeeplinkPlayerTag,$exactDeeplink);
			if ($this->logger) $this->logger->debug("silex_search.php createIndex added exact deeplink: $exactDeeplink");
		}
		else{
			if ($this->logger) $this->logger->info("silex_search.php createIndex no exact deeplink to add for this player");
		}
		
		// indexes the record
		$index->addDocument($doc);
		return $index;
	}
	/* indexComponent
	 * indexes a new document in $index with
	 * extract the data from component's script (getSingleRecord method) and places it in an array of results, at the index given by the resultContainer attribute of the component
	 * call recursively indexFilesRecursively function for all deep links returned by the component's script (getRecords method) combined to the component's deeplinkFormat and link
	 * also call indexPlayer for the component
	 */
	function indexComponent($websiteContentFolderPath,$item,$player,$index,$dataContainer_array,$deeplink=null){
		if ($this->logger) $this->logger->debug("silex_search.php indexComponent($websiteContentFolderPath,$player,index) ");
		// to do : checkRights(ROOTPATH . );
		// include the component's server side script
		$componentScriptFile=ROOTPATH . self::componentClassFolder.$player[self::seoClassNameComponentTag].".php";
		if ($this->logger) $this->logger->debug("silex_search.php indexComponent - about to include $componentScriptFile");
		try{
			include_once($componentScriptFile);
		}
		catch (Exception $e) 
		{
			if ($this->logger) $this->logger->info('Caught exception in external module (include the component\'s server side script - $componentScriptFile) : ',  $e->getMessage());
		}

		// instanciate the component's serverside class
		if ($this->logger) $this->logger->debug("silex_search.php indexComponent - about to instanciate ".$player[self::seoClassNameComponentTag]);
		try{
			$componentClass=new $player[self::seoClassNameComponentTag];
		}
		catch (Exception $e) 
		{
			if ($this->logger) $this->logger->info('Caught exception in external module (instanciate the component\'s serverside class) : ',  $e->getMessage());
		}
		//$this->logger->debug("silex_search.php indexComponent instanciate DONE");
		
		if(isset($player[self::seoComponentParamsComponentTag][self::seoResultContainerComponentTag]))
			$_resultContainerPath=$player[self::seoComponentParamsComponentTag][self::seoResultContainerComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoFormNameComponentTag]))
			$formName=$player[self::seoComponentParamsComponentTag][self::seoFormNameComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoIdFieldComponentTag]))
			$idField=$player[self::seoComponentParamsComponentTag][self::seoIdFieldComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoLimitComponentTag]))
			$limit=$player[self::seoComponentParamsComponentTag][self::seoLimitComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoTemplateComponentTag]))
			$template=$player[self::seoComponentParamsComponentTag][self::seoTemplateComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoOffsetComponentTag]))
			$offset=$player[self::seoComponentParamsComponentTag][self::seoOffsetComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoOrderByComponentTag]))
			$orderBy=$player[self::seoComponentParamsComponentTag][self::seoOrderByComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoSelectedFieldNamesComponentTag]))
			$selectedFieldNames=$player[self::seoComponentParamsComponentTag][self::seoSelectedFieldNamesComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoWhereClauseComponentTag]))
			$whereClause=$player[self::seoComponentParamsComponentTag][self::seoWhereClauseComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoSelectedFieldNamesComponentTag]))
			$selectedFieldNames=$player[self::seoComponentParamsComponentTag][self::seoSelectedFieldNamesComponentTag];
			
		if(isset($player[self::seoComponentParamsComponentTag][self::seoDeeplinkFormatComponentTag]))
			$deeplinkFormat=$player[self::seoComponentParamsComponentTag][self::seoDeeplinkFormatComponentTag];
			
		$whereClause=$this->revealAccessors($whereClause,$dataContainer_array);
		$template=$this->revealAccessors($template,$dataContainer_array);

		// **
		// retrieve component links
		// function getRecords($form, $fields, $whereClause, $orderBy, $count, $offset){
		if ($this->logger) $this->logger->debug("silex_search.php indexComponent - about to call getRecords with : ".$formName.", ".$selectedFieldNames.", ".$whereClause.", ".$orderBy.", ".$limit.", ".$offset);
		try{
			$dynData=$componentClass->getRecords($formName,$selectedFieldNames,$whereClause,$orderBy,$limit,$offset);
			if ($this->logger) $this->logger->debug("silex_search.php indexComponent getRecords returned ".count($dynData)." records");
			
			// index the component
			// indexPlayer

			// find deep link
			$deeplinkSection="";
			foreach($item as $tag => $value){
				// for all tags except <content> tag
				if ($tag==self::seoDeeplinkPlayerTag){//seoLinkSectionTag
					// forces this page deeplink
					if ($deeplink==null){
						$deeplinkSection=$this->cleanId($value)."/";
					}
					else{
						$deeplinkSection=$this->cleanId($deeplink.$value)."/";
					}
				}
			}
			
			// index each results
			if ($dynData)
				foreach ($dynData as $element) {
					//$this->logger->debug("silex_search.php indexComponent getRecords returned ".print_r($element,true));
					if ($this->logger) $this->logger->debug("silex_search.php indexComponent - about to call getSingleRecord. params : $_resultContainerPath , $deeplinkFormat , $template , $idField");
					if ($_resultContainerPath){
						// store the data from getRecords call
						foreach ($element as $tmp_key => $tmp_value)
							$dataContainer_array[$_resultContainerPath.".".$tmp_key]=$tmp_value;
						
						// call getSingleRecord
						$tmp_array=$componentClass->getSingleRecord($formName,$element[$idField]);
						// add results to dataContainer_array
						if ($tmp_array)
							foreach ($tmp_array as $tmp_key => $tmp_value)
								$dataContainer_array[$_resultContainerPath.".".$tmp_key]=$tmp_value;
								
						//$this->logger->debug("silex_search.php indexComponent - getSingleRecord returned ".print_r($dataContainer_array,true));
						// get deeplink
						// deeplink accessor is relative to resultContainerPath (<<name>> should be interpreted as <<gsmDataContainer.dbdata_gsm.name>>)
						$deeplink=$deeplinkSection.$this->revealAccessors($deeplinkFormat,$dataContainer_array,$_resultContainerPath);
					}
					$index=$this->indexFilesRecursively($websiteContentFolderPath,$template,$index,$dataContainer_array,$deeplink);
				}
		}catch (Exception $e) {
			if ($this->logger) $this->logger->info('Caught exception in external module (in getRecords method) : ',  $e->getMessage());
		}
		
		// indexes the record
		//$index->addDocument($doc);
		return $index;
	}
	function cleanId($str){
		return str_replace(" ",$this->sepCharForDeeplinks,$str);
	}
	/* revealAccessors
	 * replaces all accessors by the values contained in dataContainer_array
	 * @param accessorRoot	the base of the accessor : if $accessorRootis set to "gsmDataContainer.dbdata_gsm", then <<name>> will be interpreted as <<gsmDataContainer.dbdata_gsm.name>>
	 */
	function revealAccessors($input_str,$dataContainer_array,$accessorRoot=null){
		//$this->logger->debug("silex_search.php revealAccessors $input_str,$accessorRoot - ".print_r($dataContainer_array,true));
		// parse all object in $dataContainer_array
		$output_str=$this->parseObject($input_str,$dataContainer_array,"replacePathByValue","",$accessorRoot);

		// TO DO !!!!!!!!!!!!!!!
		// for all (( .. )), delete it if there is a "<<" in between
		$output_str=str_replace("((","",$output_str);
		$output_str=str_replace("))","",$output_str);
		
		// clean id
		//$output_str=str_replace(" ",self::sepchar,$output_str);

		
		//$this->logger->debug("silex_search.php revealAccessors $input_str -> $output_str");
		return $output_str;
	}
	function parseObject($input_str,$obj,$callback,$path="",$arg=null){
		//$this->logger->debug("silex_search.php parseObject $input_str - ".print_r($obj,true));

		if (is_array($obj)){
			foreach($obj as $key => $value){
				if ($path!="")
					$newPath=$path.".".$key;
				else
					$newPath=$key;
				if (is_array($value)){
					$input_str=$this->parseObject($input_str,$value,$callback,$newPath,$arg);
				}
				else{
					$input_str=$this->$callback($input_str,$newPath,$value,$arg);
				}
			}
		}
		return $input_str;
	}
	// replace its <<path>> by the value
	function replacePathByValue($input_str,$path,$value,$accessorRoot=null){
		//$this->logger->debug("silex_search.php replacePathByValue $input_str , $path , $value, $accessorRoot -> ".str_replace("<<".$path.">>",$value,$input_str));

		// remove accessor root from path
		if ($accessorRoot!=null) $path=str_replace($accessorRoot.".","",$path);		
		
		$input_str=str_replace("&lt;&lt;".$path."&gt;&gt;",$value,$input_str);
		return str_replace("<<".$path.">>",$value,$input_str);
	}
///////////////////////////		
	function addTagToDoc($doc,$tag,$arg_value){
		if ($this->logger) $this->logger->debug("silex_search.php addTagToDoc(doc,$tag,$arg_value) ");

		$docContent='utf-8';
	
		//  convert input to string
		if (is_string($arg_value))
			$value=$arg_value;
		else if (is_array($arg_value))
			$value=implode($arg_value,",");
		else return false;
			
		//$this->logger->debug("silex_search.php addTagToDoc				".$tag." = ".$value);
		// Keyword: Stored + Indexed but not Tokenized
		if ($this->isInArray($tag,$this->Keyword)==true){
			
			// keep only letters since zend would replace it by spaces
			$value=preg_replace('~[^-a-z]+~', '', $value);
			
			// adds the item to the record
			$doc->addField(Zend_Search_Lucene_Field::Keyword($tag, strip_tags($value), $docContent));
			if ($this->logger) $this->logger->debug("  - ".$tag." = ".$value." stored as KEYWORD");
		}
		// UnStored: Indexed + Tokenized + Binary but not stored
		else if ($this->isInArray($tag,$this->UnStored)==true){
			// adds the item to the record
			$doc->addField(Zend_Search_Lucene_Field::UnStored($tag, $value, $docContent));
		}
		// UnIndexed: only Stored
		else if ($this->isInArray($tag,$this->UnIndexed)==true){
			// adds the item to the record
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed($tag, $value, $docContent));
		}
		// Text: Stored + Indexed + Tokenized + strip_tags
		else if ($this->isInArray($tag,$this->Text)==true){
			// adds the item to the record
			//$cleanValue = htmlentities(strip_tags( $value ));
			$doc->addField(Zend_Search_Lucene_Field::Text($tag, strip_tags($value), $docContent));
		}
		// HtmlText: Stored + Indexed + Tokenized
		else if ($this->isInArray($tag,$this->Text)==true){
			// adds the item to the record
			//$cleanValue = htmlentities(strip_tags( $value ));
			$doc->addField(Zend_Search_Lucene_Field::Text($tag, $value, $docContent));
		}
		else{
			// do nothing
			if ($this->logger) $this->logger->info("  - ".$tag." = ".$value." UNUSED");
		}
		return $value;
	}
	////////////////////////////////////////////////////////////////
	/*    <item>
	        <title>title of the silex page</title>
	        <pubDate>Mon, 26 Nov 2007 12:30:39 +0100</pubDate>
	        <category>news</category>
	        <keywords>... ... needle ... ... </keywords>
	    </item>
	*/
	function arrayToRssItems($hits,$allow_duplicate=false,$urlBase=null,$pubDate=null){
		if ($this->logger) $this->logger->debug("silex_search.php arrayToRssItems(".$hits.",$allow_duplicate) ");
		$res_str="";
				
		// array to eliminate duplicated deeplinks
		$foundDeeplinks=Array();				

		if ($hits){
			foreach ($hits as $hit) {

				// retrieve record data
				$fields=$hit->getDocument();
				
				// build rss item
				$rssItem=Array();
				
				foreach ($fields->getFieldNames() as $tag) {
					$rssItem[$tag]=$hit->$tag;
				}
				$search==null;
				if (isset($rssItem["deeplink"])){
					$search=array_search($rssItem["deeplink"],$foundDeeplinks);
				}
					
				if ($allow_duplicate===false && $search!==FALSE && $search!==NULL){
					// deeplink allready seen
					if ($this->logger) $this->logger->debug("silex_search.php arrayToRssItems ".$rssItem["deeplink"]." allready seen ");
				}
				else{

					// store the deeplink in $foundDeeplinks
					if ($allow_duplicate===false){
						if ($this->logger) $this->logger->debug("silex_search.php arrayToRssItems ".$hit->deeplink." found ");
						$foundDeeplinks[]=$rssItem["deeplink"];
					}
					$res_str.="
			<item>";
					foreach ($rssItem as $tag => $value) {
						if ($tag!=self::seoPubDateSectionTag || $pubDate==null)
						{
						$res_str.="
					<".$tag."><![CDATA[".$hit->$tag."]]></".$tag.">";
						}
					}
					if ($pubDate!=null)
					{
						$res_str.="
					<".self::seoPubDateSectionTag."><![CDATA[".$pubDate."]]></".self::seoPubDateSectionTag.">";
					}
					
					// description is text if no description is provided but only text
					if (isset($rssItem["description"])==false && isset($rssItem["text"])==true){
						//$res_str.="<description><![CDATA[".$this->lastQuery->highlightMatches(iconv('UTF-8', 'ASCII//TRANSLIT', $hit->text))."]]></description>";
						if (isset($rssItem["htmlEquivalent"])==true)
							$res_str.="<description><![CDATA[".$rssItem["htmlEquivalent"]."]]></description>";
						else if (isset($rssItem["text"])==true)
							$res_str.="<description><![CDATA[".$rssItem["text"]."]]></description>";
					}
					// link is UrlBase+deeplink if no link is provided
					if (isset($rssItem["link"])==false && isset($rssItem["deeplink"])==true){
						if($urlBase!=null)
							$urlBase_tmp=$urlBase;
						else if (isset($rssItem["urlBase"])==true)
							$urlBase_tmp=$rssItem["urlBase"];
						else $urlBase_tmp=null;

						if ($urlBase_tmp!=null)
							$res_str.="<link><![CDATA[".$urlBase_tmp.$rssItem["deeplink"]."]]></link>";
					}
					$res_str.="
			</item>";
				}
			}
		}
		return $res_str;
	}
	function isInArray($element,$array){
		foreach($array as $value)
			if ($value==$element)
				return true;
				
		return false;
	}
	function buildSeoDataObject($hits,$deeplink,$urlBase=null){
		if (!$hits) return null; 
		if ($this->logger) $this->logger->debug("silex_search.php buildSeoDataObject urlBase=$urlBase - deeplink=$deeplink");
		// build an array <=> seoData object
		$seoData=Array();
		foreach ($hits as $hit) {
			//if ($this->logger) $this->logger->debug("silex_search.php buildSeoDataObject adding ".$hit->title);
			// ** 
			// retrieve record data
			$fields=$hit->getDocument();
			
			// ** 
			// build an array for this item
			$item=Array();
			foreach ($fields->getFieldNames() as $tag) {
				$item[$tag]=$hit->$tag;
			}
			if ($this->logger) $this->logger->debug("silex_search.php buildSeoDataObject LINK ".$item["link"]);
			// ** 
			// check if this item corresponds to the search - workaround strange  deeplinks (with numbers, '_', ...)
			//if (isset($item["deeplink"])==true && ($item["deeplink"]==$deeplink){
				// ** 
				// Fill the blanks in item object
				// description is text if no description is provided but only text
				/*
				if (isset($item["description"])==false && isset($item["text"])==true){
					$item["description"]=$item["text"];
				}
				*/
				if (isset($item["description"])==false){
					$item["description"]="";
				}
				
				// link is UrlBase+deeplink if no link is provided
				if (isset($item["link"])==false && isset($item["deeplink"])==true){
					if($urlBase!=null)
						$urlBase_tmp=$urlBase;
					else if (isset($item["urlBase"])==true)
						$urlBase_tmp=$item["urlBase"];
					else $urlBase_tmp=null;

					if ($urlBase_tmp!=null)
						$item["link"]=$urlBase_tmp.$item["deeplink"];
				}
				// ** 
				// build the result seoData
				foreach ($item as $tag => $value) {
					switch($tag){
						case "link":
						case "deeplink":
						case "urlBase":
						case "pubDate":
						case "title":
							// simply replace value
							$seoData[$tag]=$item[$tag];
						break;
						case "context": // add contexts with a coma as separator
							// if the tag does not exists, create it
							if(isset($seoData[$tag])==false) $seoData[$tag]="";
							// if the tag allready exists, add a separator
							else $seoData[$tag].=",";
							$seoData[$tag].=$item[$tag];
						break;
/*						case "links":
							// if the tag does not exists, create it
							if(isset($seoData[$tag])==false) $seoData[$tag]="";
							// if the tag allready exists, add a separator
							else $seoData[$tag].="<br>";
							// build a string
							foreach ($value as $linkObject) {
								$this->logger->debug("silex_search.php buildSeoDataObject links for ".$item["title"]." : ".$linkObject["context"]);
								$seoData["links"].="<a context='".$linkObject["context"]."' href='".$linkObject["title"]."'>".$linkObject["link"]."</a>";
							}
						break;*/
						default: // add texts with a <br> as separator (htmlEquivalent, text, description)
							if ($this->logger) $this->logger->debug("silex_search.php buildSeoDataObject new tag for ".$item["title"]." : $tag=".$item[$tag]);
							// if the tag does not exists, create it
							if(isset($seoData[$tag])==false || $seoData[$tag]=="") $seoData[$tag]="";
							// if the tag allready exists, add a separator
							else $seoData[$tag].=", ";
							$seoData[$tag].=$item[$tag];
					}
				}
/*			}
			else{
				$this->logger->debug("silex_search.php buildSeoDataObject ".$item["deeplink"]." is different form the desired deeplink ($deeplink)");
			}*/
		}
		// replace <a context='' href='a.propos.de.ce.cv/'>a.propos.de.ce.cv</a> par <a context='' href='http://localhost:80/repositories/silex-trunk/silex_server/?/test/start/a.propos.de.ce.cv/'>a.propos.de.ce.cv</a>
		$seoData["links"]=str_replace("href='","href='".$urlBase.$seoData["deeplink"],$seoData["links"]);
		// return
		return $seoData;
	}
}
?>