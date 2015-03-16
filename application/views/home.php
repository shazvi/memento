<?php date_default_timezone_set (TIMEZONE); // for blink function
/*LINKS:
https://www.google.com/search?q=table+edit+inline
http://vitalets.github.io/x-editable/docs.html*/
?>

<div class="container navbar" style="padding-top: 5px;">
    <div class="navbar-inner">
        <div class="pull-left">
            <a data-toggle="modal" href="/new.php" class="btn"><i class="icon-file"></i> New Note</a>
            <a href="#" class="btn" onclick="multidelbtn()"><i class="icon-trash"></i> Delete</a>
            <span class="btn-group">
                <a href="#" class="btn dropdown-toggle" data-toggle="dropdown" rel="tooltip" title="Not yet functional">
                    <i class="icon-share"></i> Move to: <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach ($bookrows as $key): ?>
                        <li><a href="#"><?=$key["bookname"]?></a></li>
                    <? endforeach ?>
                </ul>
            </span>
        </div>

        <span class="form-search">
            <div class="input-append">
                <input class="input-medium search-query" autofocus id="search" name="search" placeholder="Search..." type="text" rel="tooltip" data-placement="bottom" title="Type to search, 'Esc' to clear"/>
                <button class="btn" title="Clear" onclick="$('#search').val('').keyup().focus();"><i class="icon-remove"></i></button>
            </div>
        </span>

        <script type="text/x-template" id="navbar-right">
            <div class="pull-right">
                <span class="btn-group" id="books">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="icon-list"></i> Notebook: <strong><?= $book->bookname?></strong>
                        <span class="badge badge-info"><?=count($positions)?></span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a data-toggle="modal" href="#prompt" onclick="promptvar='new'">New <strong>NoteBook</strong></a></li>
                        <li class="divider"></li>
                        <?php foreach ($bookrows as $key): ?>
                            <li class="dropdown-submenu<?php if($book->booknum==$key["booknum"]) echo" active";?>">
                                <a tabindex="-1" href="notebook.php?snum=<?= $key["booknum"]?>"><?= $key["bookname"] ?></a>
                                <ul class="dropdown-menu">
                                    <li><a onclick="$('#prinput').val('<?= $key["bookname"]?>'); promptvar='rename'; renamnum= <?= $key['booknum']?>;" data-toggle="modal" href="#prompt">Rename</a></li>
                                    <li><a <?php if($this->session->userdata("id")!=JOHNID):?>onclick="$('#edel').attr('href','notebook.php?dnum=<?= $key["booknum"]?>')"<? endif?> data-toggle="modal" href="#deletefield">Delete</a></li>
                                </ul>
                            </li>
                        <? endforeach ?>
                    </ul>
                </span>
    
                <?php $phpblink = $this->session->userdata("blink");
                if(!empty($phpblink) && $phpblink==0):?>
                    <button type="submit" class="btn" title="Blinking is off" onclick="blinkajax(this);">
                        <i class='icon-ok-circle'></i> <strong>Turn on</strong> Blinking
                    </button>
                <?else:?>
                    <button type="submit" class="btn" title="Blinking is on" onclick="blinkajax(this);">
                        <i class='icon-ban-circle'></i> <strong>Turn off</strong> Blinking
                    </button>
                <?endif?>
            </div>
        </script>
        
    </div>
</div>

<table class="table table-striped" id="table0">
    <thead class="head">
        <tr>
            <th class="sorttable_nosort" style="">
                <input type="checkbox" title="Select All" onchange="checktick(this);">
            </th>
            <th>Date Set</th>
            <th title="Priority" style="white-space:nowrap;"><i class="icon-tasks"></i></th>
            <th>Reminders/Memos/Notes <i rel="tooltip" class="icon-info-sign" title="Click header to sort, drag handle to reorder"></i></th>
            <th>Tags</th>
            <th class="sorttable_nosort"></th>
            <th>Date Due</th>
            <th rel="tooltip" data-placement="left" title="Send email confirmation on due date">Send Email</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($positions as $row): ?>
        <tr>
            <td style="white-space:nowrap;">
                <input type="checkbox" class="chkbody" onchange="bluetick(this);">
                <span class="btn-group buttns">
                    <!-- <a class="btn btn-mini btn-primary" title="Edit" href="/edit.php?link=<?=$row["number"]?>" data-editid="<?=$row["number"]?>"><i class="icon-pencil icon-white"></i></a> -->
                    <a class="btn btn-mini pdd" title="Click to recycle, SHIFT click to delete" onclick="deletebtn(event,<?=$row["number"]?>,'recycle.php')" onmouseover="delmouseover(event,this)" onmouseout="delmouseout(this)" data-loading-text="Deleting..." data-delid="<?=$row["number"]?>"><i class="icon-trash"></i></a>
                </span>
                
            </td>
            <td style="white-space:nowrap;"><?= $row["dateset"] ?></td>
            <td style="">
                <?php if ($row["priority"] == "high") : ?>
                    <a class="priorclass" data-type="select" data-value="high" data-pk="<?php echo $row['number']?>" data-url="<?php echo base_url();?>post/editpriority" href="#"><span class="label label-important"><i title="High" class=" icon-info-sign icon-white"><span style="visibility: hidden;">a</span></i></span></a>
                <? elseif ($row["priority"] == "medium") : ?>
                    <a class="priorclass" data-type="select" data-value="medium" data-pk="<?php echo $row['number']?>" data-url="<?php echo base_url();?>post/editpriority" href="#"><span class="label label-warning"><i title="Medium" class=" icon-info-sign icon-white"><span style="visibility: hidden;">b</span></i></span></a>
                <? elseif ($row["priority"] == "low") : ?>
                    <a class="priorclass" data-type="select" data-value="low" data-pk="<?php echo $row['number']?>" data-url="<?php echo base_url();?>post/editpriority" href="#"><span class="label label-success"><i title="Low" class=" icon-info-sign icon-white"><span style="visibility: hidden;">c</span></i></span></a>
                <? endif ?>
            </td>
            <td>
                <div <?php if((strtotime($row["duedate"]) > time()) && (strtotime($row["duedate"])< (time()+86400))) echo'class="blink"';?>>
                    <i class="handle icon-align-justify" style="cursor: move;"></i>
                    <span class="classes">
                        <a href="#" class="noteclass" data-type="textarea" data-rows="3" data-pk="<?=$row["number"]?>" data-url="<?php echo base_url();?>post/editnote"><?=$row["note"]?></a>
                    </span>

                </div>
            </td>
            <td style="white-space:nowrap;">
                <div class="tagstd">
                    <?php foreach ($tags as $tag):?>
                        <?php if((trim($tag["tags"]) != '')&&($tag["number"] == $row["number"])):?>
                            <div class='glowy glow' style='cursor: pointer;' onclick="toinput(this, event);" title='Click to edit, Ctrl-click to search by tag'><?=$tag["tags"];?>,</div>
                        <?endif?>
                    <?endforeach?>
                </div>
            </td>
            <td style="white-space:nowrap;">
                <div style="display: none;" class="tagsfields">
                    <input type="text" class="typeahead" value="" style="margin-bottom: 2px;" autocomplete="off"/>
                    <span class="btn-group">
                        <a onclick="savetagbox(this)" data-id="<?=$row["number"]?>" title="Save" class="btn btn-small btn-primary"><i class="icon-ok icon-white"></i></a>
                        <a onclick="canceltagbox(this)" title="Cancel" class="btn btn-small"><i class="icon-remove"></i></a>
                    </span>
                </div>
            </td>
            <td style="white-space:nowrap;">
                <a href="#" class="duedateclass" data-type="datetime" data-pk="<?=$row["number"]?>" data-url="<?php echo base_url();?>post/editduedate"><?php if ($row["duedate"] == "0000-00-00 00:00") echo "---"; else echo $row["duedate"];?></a>
            </td>
            <td>
                <a href="#" class="confclass" data-type="select" data-value="<?php echo $row['email']?>" data-pk="<?=$row["number"]?>" data-url="<?php echo base_url();?>post/editconf"><?php echo $row["email"];?>
            </td>
        </tr>

    <? endforeach ?>
    </tbody>
</table>


<!--   MODALS   -->
<div id="deletefield" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Are you sure?</h3>
    </div>
    <div class="modal-body"><p>Deleted notes cannot be recovered.<br>Are you sure you want to continue?</p></div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</a>
        <a class="btn btn-danger" id="edel" href="#">Delete</a>
    </div>
</div>

<div id="prompt" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <a href="#" class="close" data-dismiss="modal">&times;</a>
        <h3>Enter name of notebook</h3>
    </div>
    <div class="modal-body">
        <input type="text" value="" id="prinput"/>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</a>
        <a id="edial" class="btn btn-primary" onclick="prmptchk()">Create Book</a>
    </div>
</div>

<div id="newnote" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
    <div class="modal-header">
        <a href="#" class="close" data-dismiss="modal" onclick="//$('#prinput').val(''); promptvar=null; renamnum=null">&times;</a>
        <h3>New Note</h3>
    </div>
    <div class="modal-body">
        <textarea placeholder="Note..."></textarea><br>
        <input type="text" value="" id="" placeholder="Tags"/><br>
        <select>
            <option selected disabled>Notebook</option>
        </select><br>
        <input type="datetime-local"/><br>
        Email: <input type="checkbox"><br>
        <select>
            <option selected disabled>Priority</option>
        </select>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true" onclick="//$('#prinput').val(''); promptvar=null; renamnum=null"
            >Cancel</a>
        <a class="btn btn-primary" onclick="//prmptchk()">OK</a>
    </div>
</div>

<script type="text/javascript">//////////////////////////////////////////    SCRIPTS     ////////////////////////////////////////////////
//turn to inline mode
//$.fn.editable.defaults.mode = 'inline';
$(function($) {
    // this bit needs to be loaded on every page where an ajax POST may happen
    $.ajaxSetup({
        data: {
            <?php echo $this->security->get_csrf_token_name(); ?> : '<?php echo $this->security->get_csrf_hash(); ?>'
        }
    });
});

function canceltagbox(el) {
    $(el).parent().parent().hide().parent().prev().children(".tagstd").show();
}

function savetagbox(el) {
    $(el).parent().parent().parent().prev().children().html("");
    var boxvalue = $(el).parent().parent().find("input").val() ;
    if(trim(boxvalue) != "") {
        $.ajax({
            type: "POST",
            url: "<?php echo base_url();?>post/edittags",
            data: {
                noteid: $(el).attr("data-id"),
                tags: boxvalue,
                booknum: <?= $book->booknum?>
            },
            success: function(returnData){
                var boxarray = boxvalue.split(",");
                $(boxarray).each(function(){
                    if( trim(this) != "" ) {
                        // Create the new element
                        var div = document.createElement('div');
                        div.className = 'glowy glow'; // Class name
                        div.innerHTML = this+","; // Text inside
                        div.style.cursor = "pointer";
                        $(div).attr("title", 'Click to edit, Ctrl-click to search by tag');
                        $(el).parent().parent().parent().prev().children().append(div); // Append it
                        div.onclick = function(){
                            toinput(div, event); // Attach the event!
                        };
                    }
                });
            }
        });

    } else {
        $(el).parent().parent().parent().prev().children().css("font-style", "italic").text("Empty");
    }
    $(el).parent().parent().hide().parent().prev().children(".tagstd").show();
}

function toinput(el, evt) {
    if(evt.ctrlKey==1) {
        if( $(el).parent().css("font-style") != "italic" ) {
            insertText($(el).text());
        }
    } else {
        var tags = $(el).parent().text();
        if( $(el).parent().css("font-style") != "italic" ) { // if tags isnt empty
            $(el).parent().parent().next().children().children().eq(0).val(trimcomma(tags)+",");
        }
        $(el).parent().hide().parent().next().children().eq(0).show();

        !function(source) {
            function extractor(query) {
                var result = /([^,]+)$/.exec(query);
                if(result && result[1])
                    return result[1].trim();
                return '';
            }

            $('.typeahead').typeahead({
                source: source,
                updater: function(item) {
                    return this.$element.val().replace(/[^,]*$/,'')+item+',';
                },
                matcher: function (item) {
                    var tquery = extractor(this.query);
                    if(!tquery) return false;
                    return ~item.toLowerCase().indexOf(tquery.toLowerCase())
                },
                highlighter: function (item) {

                    var query = extractor(this.query).replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
                    return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
                        return '<strong>' + match + '</strong>'
                    })
                }
            });

        }([
            <?php foreach($tagsarray as $tagskey) {
                echo '"'.$tagskey.'",';
            }?>
        ]);
    }
}


// Search Box Functions
$(document).ready(function() {
    $('.noteclass').editable({
        inputclass: 'mytextarea',
        mode: "inline"
    });

    $('.duedateclass').editable({
        emptytext: '---',
        success: function(response){
            if(JSON.parse(response).value == ""){
                $(this).parent().parent().find(".classes").parent().removeClass("blink").fadeIn("slow");
            } else {
                $(this).parent().parent().find(".classes").parent().addClass("blink");
            }
        },
        placement: "left"
    });

    $('.priorclass').editable({
        source: [{value: "high", text: "High"}, {value: "medium", text: "Medium"}, {value: "low", text: "Low"}],
        display: function() {
            return false;   //disable this method
        },
        success: function(response) {
            $(this).html('<span class="label label-'+JSON.parse(response).label+'"><i title="'+JSON.parse(response).value+'" class=" icon-info-sign icon-white"><span style="visibility: hidden;">'+JSON.parse(response).hidden+'</span></i></span>');
        },
        showbuttons: false
        //defaultValue: ($(this).find(".priorclass").text() == "a")?"high":(($(this).find(".priorclass").text() == "b")?"medium":"low")
    });

    $('.confclass').editable({
        source: [{value: "YES", text: "YES"}, {value: "NO", text: "NO"}],
        showbuttons: false,
        placement: "left"
    });


    $("#search").keyup(function(e) {
        $('.classes').unhighlight();
        if (e.keyCode == 27) {// clear search field on "Esc" key
            document.getElementById("search").value = "";
        }

        // Instant Search Functionality
        var val = $(this).val().toLowerCase().split(" ");
        var valtrim = trim($(this).val().toLowerCase());

        $("#table0 tbody tr").hide().each(function() {//for each row (tr)
            var text = $(this).find(".classes").text().toLowerCase();
            if( !$(this).find(".glowy").html().indexOf("<i>") != -1 ) {
                text = text + $(this).find(".glowy").text().toLowerCase();
            }
            for (var i = 0; i < val.length; i++) {//for each word in search field
                if ((text.indexOf(val[i]) != -1 && val[i] != "") || valtrim == "") {//if match found OR search box empty
                    $(this).show();
                }
            }
        });

        // HIGHLIGHT (http://bartaz.github.io/sandbox.js/jquery.highlight.html)
        $('.classes').highlight(val);
    });

    // Fix for wierd rendering glitch
    $(".navbar-inner").append($("#navbar-right").html());
});


//Delete/shift delete button functions
function deletebtn(event,num,url) {
    if(event.shiftKey==1){
        <?php if($this->session->userdata("id")!=JOHNID):?>
            $("#edel").on("click", function(e){
                e.preventDefault();
                deletebtn({shiftKey: 0}, num, "delete.php");
                $('#deletefield').modal("hide");
            });
            $('#deletefield').modal({
                keyboard: false,
                backdrop: "static"
            });
        <?endif?>
    }else{
        $('*[data-delid='+num+']').button('loading');
        $.ajax({
            type: "POST",
            url: url,
            data: {link: num},
            success: function(returnData){
                if(returnData){
                    if(JSON.parse(returnData).worked){
                        $('*[data-id='+num+']').closest("tr").fadeOut(1000, function(){
                            $(this).remove();
                            $(".badge-info")[0].innerHTML--;
                        });
                    }else{
                        $('*[data-delid='+num+']').button('reset'); // $('*[data-editid='+num+']').button('reset');
                        var td = $('*[data-id='+num+']').closest("td").append("<span class='alert alert-error'> Couldn't Access Note.</span>");
                        setTimeout(function(){
                            td.children(".alert").fadeOut(1000, function(){
                                $(this).remove();
                            });
                        }, 3500);
                        $('*[data-delid='+num+']').button('reset');
                    }
                }
            }
        });
    }
}
function multidelbtn(){
    $(".chkbody:checked").each(function(){
        var num = $(this).closest("tr").find("[data-id]").attr("data-id")*1;
        deletebtn({shiftKey: 0}, num, "recycle.php");
    });
}
$('#deletefield').on('hidden', function () {
    $("#edel").off("click");
    $('#edel').attr('href','#');
});

var hoverElem = null;
$(window).keydown(function(evt) {
    if ((evt.which == 16)&&(hoverElem != null)) {
        $(hoverElem).addClass('btn-danger');
        $("#table0").css({
            '-moz-user-select':'none',
            '-o-user-select':'none',
            '-khtml-user-select':'none',
            '-webkit-user-select':'none',
            '-ms-user-select':'none',
            'user-select':'none'
        });
    }
}).keyup(function(evt) {
        if ((evt.which == 16)&&(hoverElem != null)) {
            $(hoverElem).removeClass('btn-danger');
        }
        $("#table0").removeAttr("style");
});
function delmouseover(event,x){
    hoverElem = x;
    if (event.shiftKey==1) {
        $(x).addClass('btn-danger');
    }
}
function delmouseout(x){
    hoverElem = null;
    $(x).removeClass('btn-danger');
}


//Check empty date column and email
function hidecol(){
    $('#table0 th').each(function(i) {
        var remove = 0;
        var tds = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')');
        tds.each(function() { if ((this.innerHTML == '')||(this.innerHTML == '---')) remove++; });
        if (remove == ($('#table0 tr').length - 1)) {
            $(this).hide();
            tds.hide();
        }
    });
}
//hidecol();//$("th,td").show();


// toggles visibility of reminder(Blink)
var blinkID;
if(<?php echo(!empty($phpblink))?$phpblink:1;?>){
    blink();
}
function blink(){
    $('.blink').fadeToggle("slow");
    blinkID = setTimeout(blink, 800);
}
// Blink Ajax
function blinkajax(content){
    $.ajax({
        type: "POST",
        url: "blink.php",
        data: {},
        success: function(returnData){
            if(returnData && JSON.parse(returnData).worked){
                if(JSON.parse(returnData).blink==1){
                    $(content).html("<i class='icon-ban-circle'></i> <strong>Turn off</strong> Blinking").attr("title","Blinking is on");
                    blink();
                }else{
                    $(content).html("<i class='icon-ok-circle'></i> <strong>Turn on</strong> Blinking").attr("title","Blinking is off");
                    clearInterval(blinkID);
                    $('.blink').fadeIn("slow");
                }
            }
        }
    });
}


// Initialize Tooltip and sortable
$("[rel='tooltip']").tooltip();
$('tbody').sortable({ handle: ".handle" });


//trims white space from string (search function, tags)
function trim(str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function trimcomma(str) {
    str =
        str.replace(/^[,\s]+|[,\s]+$/g, '');
    return str.replace(/\s*,\s*/g, ',');
}


//Click tags to search
function insertText(text)
{
    var elem = document.getElementById("search");

    elem.value = trimcomma(text);
    $('#search').keyup();
    elem.focus()
}


//New note and rename modal
var promptvar = null;
var renamnum = null;
function prmptchk(){
    if(promptvar=='new'){
        window.location.href="notebook.php?name="+$("#prinput").val();
    }else if(promptvar=='rename'){
        window.location.href="notebook.php?rnum="+renamnum+"&nam="+$("#prinput").val();
    }
}
$('#prompt').on('hidden', function () {
    $('#prinput').val('');
    promptvar=null;
    renamnum=null
});

//john's homepage modifications
<?php if($this->session->userdata("id")==JOHNID):?>
    $('#edel').attr('href','#');
    $('#edel').attr('title','Not available in demo');
    $('.pdd').attr('title','Click to recycle, SHIFT click to delete(not available in demo)');
<? endif ?>


// CheckBox Select All
function checktick(box){
    if(box.checked){
        $('.chkbody').filter(":visible").attr("checked", true).change();
    }else{
        $('.chkbody').filter(":visible").attr("checked", false).change();
    }
}
// Checkbox highlight
function bluetick(box){
    if(box.checked){
        $(box).closest("tr").addClass('info');
    }else{
        $(box).closest("tr").removeClass('info');
    }
}
</script>