/**   
*	Copyright <c> Claude Bernard - University Lyon 1 -  2013
* 	License : This file is part of the DataConf application, which is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License. See details at : http://liris.cnrs.fr/lionel.medini/wiki/doku.php?id=dataconf&#licensing 
*   Author: Lionel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan
*   Description: Implémentation of the worker which process query to the reasoner. This worker is only used for this purpose, it has two main functions, start the reasoner, and query it.
*	Version: 1.2				   
*   Tags:  WEB WORKERS, DEDICATED WORKER
**/

/**
* Main task
*/


		function startReasoner(jsonString) {
			send(new Date().toTimeString() + " -> start");

		/**
		* OWL ontology parsing **from JSON** using OWLReasoner
		*/
			var json;
			try {
			json = eval(jsonString);
			} catch (ex) {
			json = eval("(" + jsonString + ")");
			}

			send(new Date().toTimeString() + " -> JSON string parsed: " + typeof(json));
			var ontology = jsw.owl.json.parse({"childNodes":[]});
			send(new Date().toTimeString() + " -> OWL ontology parsed: " + ontology.axioms.length + " axioms");

		/**
		* Creating a reasoner object for the given ontology
		*/
			reasoner = new jsw.owl.BrandT(ontology);
			
			//send(new Date().toTimeString() + " -> Reasoner initialized: " + reasoner.aBox.database.ClassAssertion.length + " class assertions, " + reasoner.aBox.database.ObjectPropertyAssertion.length + " object property assertions, ");
			
			reasoner.aBox.database = json.database;
			reasoner.classHierarchy = json.classHierarchy;
			reasoner.classSubsumers = json.classSubsumers;
			//	reasoner.objectPropertyHierarchy = json.objectPropertyHierarchy;
			//	reasoner.objectPropertySubsumers = json.objectPropertySubsumers;
			reasoner.originalOntology = json.originalOntology;
			//	reasoner.timeInfo = json.timeInfo;

			send(new Date().toTimeString() + " -> Reasoner filled: " + reasoner.aBox.database.ClassAssertion.length + " class assertions, " + reasoner.aBox.database.ObjectPropertyAssertion.length + " object property assertions, ");
		}

		function queryReasoner(parameters) {
		/**
		* Creating SPARQL query
		*/

			var results = null;
			if(parameters.queryString == "getUpperLevelInstances"){
				results = getUpperLevelInstances(parameters.keyword); 
			}else if(parameters.queryString == "getLowerLevelInstances"){
				results = getLowerLevelInstances(parameters.keyword); 
			}else if(parameters.queryString == "getRecommendedKeywords"){
				results = getRecommendedKeywords(parameters.keyword);
			}else{
				var query = jsw.sparql.parse(parameters.queryString);
				send(new Date().toTimeString() + " -> query: " + parameters.queryString + " -> " + query.triples.length + " triple(s), " + query.variables.length + " variable(s)");

			/**
			* Querying the reasoner
			*/
				var results = reasoner.answerQuery(query);
				send(parameters.keyword);
			
			}
			
			send({results : results, param: parameters.param,callback :parameters.callback});
		}

		function getUpperLevelInstances(baseKeyword) {
			
			var queryText = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> select ?o { <'+baseKeyword+'>  <rdf:type> ?o }';
			var query = jsw.sparql.parse(queryText);
			var results = reasoner.answerQuery(query);
			var fatherTab = [];
			for(var i=0; i < results.length; i++){
				if(results[i].o != baseKeyword && results[i].o != "http://www.w3.org/2002/07/owlThing" && results[i].o != "http://www.w3.org/2002/07/owl#Thing" && results[i].o != "http://proton.semanticweb.org/2005/04/protontObject" && results[i].o != "http://proton.semanticweb.org/2005/04/protont#Object" ){
					fatherTab.push(results[i].o);
				}
			};
			for(var i=0; i < fatherTab.length; i++){
				
					//var query = jsw.sparql.parse('PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> select ?o { <'+classTab[i].object+'>  <rdf:type> ?o }');
					var query = jsw.sparql.parse('PREFIX owl: <http://www.w3.org/2002/07/owl#> select ?o { <'+fatherTab[i]+'> <owl:SubClassOf>  ?o }');
					var resultsTab = reasoner.answerQuery(query);
					for(var j=0; j < resultsTab.length; j++){
				
						if(resultsTab[j].object!=fatherTab[i]){
						
							var index = fatherTab.indexOf(resultsTab[j].object);
						
							if(index != -1){
								fatherTab.splice(index, 1);
							}
				
						}
					}
			};
			return fatherTab;
		}

		function getLowerLevelInstances(baseKeyword) {
			
			var queryText = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> select ?o { ?o <rdf:type> <'+baseKeyword+'> }';
			var query = jsw.sparql.parse(queryText);
			var results = reasoner.answerQuery(query);
			var sonTab = [];
			
			for(var i=0; i < results.length; i++){

				if(results[i].o != baseKeyword && results[i].o != "http://www.w3.org/2002/07/owlThing" && results[i].o != "http://www.w3.org/2002/07/owl#Thing" && results[i].o != "http://proton.semanticweb.org/2005/04/protontObject" && results[i].o != "http://proton.semanticweb.org/2005/04/protont#Object" ){
					sonTab.push(results[i].o);
				}
			}
			
			/*for(var i=0; i < sonTab.length; i++){
					send("current son");
					send(sonTab[i]);
					//var query = jsw.sparql.parse('PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> select ?o { <'+classTab[i].object+'>  <rdf:type> ?o }');
					var queryText = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> select ?o { ?o <rdf:type> <'+sonTab[i]+'> }';
					var resultsTab = reasoner.answerQuery(query);

					for(var j=0; j < resultsTab.length; j++){
						send("current son of son tab");
						send(resultsTab);
						if(resultsTab[j].object!=sonTab[i]){
							send("REMOEEEOOOQSFOQSFOSQFO");
							var index = sonTab.indexOf(resultsTab[j].object);
							send(index);
							if(index != -1){
								sonTab.splice(index, 1);
							}
							send(sonTab);
						}
					}
			
			};*/
			return sonTab;
		}

		function getRecommendedKeywords(baseKeyword) {

			var query = jsw.sparql.parse('PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> select ?o { <'+baseKeyword+'>  <rdf:type> ?o }');
			var classTab = reasoner.answerQuery(query);
			
			var JSONfile = {};
			send("baseKeyword");
			send(baseKeyword);
			send("base CLass Tab");
			send(classTab);
			var fatherTab = []
			for(var i=0; i < classTab.length; i++){
				if(classTab[i].o != baseKeyword && classTab[i].o != "http://www.w3.org/2002/07/owlThing" && classTab[i].o != "http://www.w3.org/2002/07/owl#Thing" && classTab[i].o != "http://proton.semanticweb.org/2005/04/protontObject" && classTab[i].o != "http://proton.semanticweb.org/2005/04/protont#Object" ){
					fatherTab.push(classTab[i].o);
				}
			}
			send("Upper CLass Tab");
			send(fatherTab);
			for(var i=0; i < fatherTab.length; i++){
				send(fatherTab[i]);
					

				
					//var query = jsw.sparql.parse('PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> select ?o { <'+classTab[i].object+'>  <rdf:type> ?o }');
					var query = jsw.sparql.parse('PREFIX owl: <http://www.w3.org/2002/07/owl#> select ?o { <'+fatherTab[i]+'> <owl:SubClassOf>  ?o }');
					var resultsTab = reasoner.answerQuery(query);
					send(fatherTab[i]);
					send(resultsTab);
					for(var j=0; j < resultsTab.length; j++){
						
						send(resultsTab[j].object);
						send(fatherTab[i]);
							if(resultsTab[j].object!=fatherTab[i]){
								send("REMOEEEOOOQSFOQSFOSQFO");
								var index = fatherTab.indexOf(resultsTab[j].object);
								send(index);
								if(index != -1){
									fatherTab.splice(index, 1);
								}
								send(fatherTab);
							}
						
					}
					send(i);
				send("Tab elagué");
			
			};
			
			fatherTab.push(baseKeyword);
			var finalTab = [];
			for(var i=0; i < fatherTab.length; i++){
				var query = jsw.sparql.parse('PREFIX owl: <http://www.w3.org/2002/07/owl#> select ?o { ?o <owl:SubClassOf>  <'+fatherTab[i]+'> }');
				var resultsTab = reasoner.answerQuery(query);
				for(var i=0; i < resultsTab.length; i++){
				
					finalTab.push(resultsTab[i].subject);
				}
			}
			send("Direct class tab");
			send(finalTab);

			return finalTab;
		}



