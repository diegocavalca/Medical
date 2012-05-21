    /****************************************************************************************
                                     [[ FUNCOES PARA AGENDA ]]
    ****************************************************************************************/
    function refreshAgenda(element,inputReturn){
        
        // Loading Agenda...
        $.ajax({
            url: 'http://www.needa.com.br/medical/api/agenda.php?UUID='+device.uuid,
            type: "GET",
            dataType: "jsonp",
            beforeSend: function(){
                showLoader('LOADING...');
            },
            complete: function(){
                
                hideLoader();
                // Refazendo estilo mobile
                $(element).listview('refresh');
                
            },
            success: function( response ){
                
                // Guardando retorno da consulta
                $(inputReturn).val( JSON.stringify(response) );
                 
                // Percorrendo agenda               
                $(element).remove('li');
                                
                $.each(response.agenda, function(key, agenda) {
                    
                    $('<li data-role="list-divider">'+agenda.date+'</li>').hide().appendTo(element).slideDown("slow");
                    
                    // Compromissos do dia
                    $.each(agenda.item, function(key, item) {

                        // Dados do Item
                        var itenDetail = '<li><h3>'+item.name+'</h3><p><strong>'+item.type+'</strong></p><p>'+item.legend+'</p><span class="ui-li-count">'+item.hour+'</span><ul data-filter="true" data-inset="true"><li data-role="list-divider">Observa&ccedil;&atilde;o</li><li>'+item.obs+'</li><li data-role="list-divider">Procedimento</li>';
                        
                        
                        // Procedimentos
                        $.each(item.procedure, function(key, procedure) {
                           itenDetail = itenDetail + '<li>'+procedure.description+'</li>';
                        });
                        
                        
                        itenDetail = itenDetail + '<li><a href="index.html" data-transition="none" data-icon="back" data-direction="reverse" inline="true"><b>Voltar</b></a></li></ul>';
                        
                        // Inserindo na Lista
                        $(itenDetail).hide().appendTo("#listAgenda").fadeIn("slow");
                        
                        //$('<li><h3>'+item.name+'</h3><p><strong>'+item.type+'</strong></p><p>'+item.legend+'</p><span class="ui-li-count">'+item.hour+'</span></li>').hide().appendTo("#listAgenda").slideDown("slow");
                        
                    });
                    
                });         
                
            },
            error:function(xhr,err){
                alert("responseText: "+xhr.responseText); //alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
            }
        });

    }

    function syncAgenda(value){
        
        // Verificar se possui algo 
        if(value!=""){
            
            var db = window.openDatabase("medical", "1.0", "Database Medical", 200000);
            db.transaction(populateDB, errorCB, successCB);
            
            function populateDB(tx) {
            
                var registro = value;
                
                tx.executeSql('CREATE TABLE IF NOT EXISTS backup (type, data)');
                tx.executeSql('DELETE FROM backup WHERE type="AGENDA"');                
                tx.executeSql("INSERT INTO backup (type, data) VALUES ('AGENDA','"+registro+"')");
                
                alert('Sincronização realizada com sucesso!' );
            }
            
        }else{
            alert("#INFO: Não possui dados para sincronizar!");            
        }
                            
    }                   
    
    function listAgenda(element){
        
        showLoader('LOADING...');
        
        // Variavel de retorno
        var db = window.openDatabase("medical", "1.0", "Database Medical", 200000);
        db.transaction(queryDB, errorCB);
        
        function queryDB(tx) {
            tx.executeSql('SELECT * FROM backup WHERE type="AGENDA"', [], querySuccess, errorCB);
        }
        
        function querySuccess(tx, results) {
            
            var len = results.rows.length;
            if(len>0){
                
                // Listando os resultados do BACKUP
                var response = $.parseJSON( results.rows.item(0).data );
                
                $(element).remove('li');
                
                $.each(response.agenda, function(key, agenda) {
                    
                    $('<li data-role="list-divider">'+agenda.date+'</li>').hide().appendTo(element).slideDown("slow");
                    
                    // Compromissos do dia
                    $.each(agenda.item, function(key, item) {

                        $('<li><a href="teste.html"><!--<img src="'+item.image+'" width="115" height="115" />--><h3>'+item.name+'</h3><p>'+item.legend+'</p><span class="ui-li-count">'+item.hour+'</span></a></li>').hide().appendTo(element).slideDown("slow");
                        
                    });
                    
                }); 
                
                // Refazendo estilo mobile
                $(element).listview('refresh');

            }else{
                alert('#INFO: Não foram encontrados dados para esta requisição!');                
            }
        }
        
        hideLoader();
        
    }
    /****************************************************************************************
                         [[ ================= FIM ================= ]]
    ****************************************************************************************/