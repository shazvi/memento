<div class="pull-left">
    <button class="btn" onclick="if($('.chkbody:checked').length)multidelbtn('restore')"><i class="icon-share"></i> Restore Selected</button>
    <button class="btn btn-danger" <?php if($_SESSION["id"]!=JOHNID):?>onclick="if($('.chkbody:checked').length)delconfirm('multi');"<?endif?>><i class="icon-trash"></i> Delete Selected</button>
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
        <th>Notebook</th>
        <th rel="tooltip" data-placement="left" title="Send email confirmation on due date">Send Email</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($positions as $row): ?>
        <tr>
            <td style="white-space:nowrap;">
                <input type="checkbox" class="chkbody" onchange="bluetick(this);">
            </td>
            <td style="white-space:nowrap;"><?= $row["dateset"] ?></td>
            <td style="">
                <?php if ($row["priority"] == "high") : ?>
                    <span class="label label-important"><i title="High" class=" icon-info-sign icon-white"><span style="visibility: hidden;">a</span></i></span>
                <? elseif ($row["priority"] == "medium") : ?>
                    <span class="label label-warning"><i title="Medium" class=" icon-info-sign icon-white"><span style="visibility: hidden;">b</span></i></span>
                <? elseif ($row["priority"] == "low") : ?>
                    <span class="label label-success"><i title="Low" class=" icon-info-sign icon-white"><span style="visibility: hidden;">c</span></i></span>
                <? endif ?>
            </td>
            <td>
                <div>
                    <span class="classes">
                        <?=$row["note"]?>
                    </span>
                    <span class="btn-group buttns">
                        <a class="btn btn-mini" title="Restore" onclick="ajaxbtn(<?=$row["number"]?>, 'restore')" data-loading-text="Restoring..." data-id="<?=$row["number"]?>">Restore</a>
                        <a class="btn btn-mini pdd btn-danger" data-id="<?=$row["number"]?>" <?php if($_SESSION["id"]!=JOHNID):?>onclick="delconfirm(<?=$row["number"]?>)"<?endif?> data-loading-text="Deleting...">Delete</a>
                    </span>
                </div>
            </td>
            <td style="white-space:nowrap;">
                <?php foreach ($tags as $tag):?>
                    <?php if((trim($tag["tags"]) != '')&&($tag["number"] == $row["number"])):?>
                        <div class='glow'><?=$tag["tags"];?>,</div>
                    <?endif?>
                <?endforeach?>
            </td>
            <td style="white-space:nowrap;">
                <?php if ($row["duedate"] == "0000-00-00 00:00") echo "---"; else echo $row["duedate"];?>
            </td>
            <td>
                <?php foreach ($bookrow as $key) {
                    if ($row["booknum"] == $key["booknum"]) echo $key["bookname"];
                } ?>
            </td>
            <td>
                <?php echo $row["email"];?>
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
        <a class="btn btn-danger" id="edel" href="">Delete</a>
    </div>
</div>

<script type="text/javascript">//////////////////////////////////////////    SCRIPTS     ////////////////////////////////////////////////
    // Initialize Tooltip and sortable
    $("[rel='tooltip']").tooltip();
    //$('tbody').sortable();
    $("#rectab :eq(0)").append(" <span class='badge badge-info'><?=count($positions)?></span>");

    //trims white space from string (tags)
    function trim (str) {
        return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    }

    //Delete/shift delete button functions
    function ajaxbtn(num,url){
        $('*[data-'+url+'id='+num+']').button("loading");
        $.ajax({
            type: "POST",
            url: url+".php",
            data: {link: num},
            success: function(returnData){
                if(returnData){
                    if(JSON.parse(returnData).worked){
                        $('*[data-id='+num+']').closest("tr").fadeOut(1000, function(){
                            $(this).remove();
                        });
                    }else{
                        $('*[data-'+url+'id='+num+']').button("reset");
                        var td = $('*[data-id='+num+']').closest("td").append("<span class='alert alert-error'> Couldn't Access Note.</span>");
                        setTimeout(function(){
                            td.children(".alert").fadeOut(1000, function(){
                                $(this).remove();
                            });
                        }, 3500)
                    }
                }
            }
        });
    }
    function delconfirm(num){
        $("#edel").on("click", function(e){
            e.preventDefault();
            if(num=="multi"){
                multidelbtn("delete")
            }else{
                ajaxbtn(num, "delete");
            }
            $('#deletefield').modal("hide");
        });
        $('#deletefield').modal({
            keyboard: false,
            backdrop: "static"
        });
    }
    function multidelbtn(url){
        $(".chkbody:checked").each(function(){
            var num = $(this).closest("tr").find("[data-deleteid]").attr("data-deleteid")*1;
            ajaxbtn(num, url);
        });
    }
    $('#deletefield').on('hidden', function () {
        $("#edel").off("click").attr('href','#');
    });


    //Check empty date column and email
    $('#table0 th').each(function(i) {
        var remove = 0;
        var tds = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')')
        tds.each(function() { if ((this.innerHTML == '')||(this.innerHTML == '---')) remove++; });
        if (remove == ($('#table0 tr').length - 1)) {
            $(this).hide();
            tds.hide();
        }
    });

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

    //john's homepage modifications
    <?php if($_SESSION["id"]==JOHNID):?>
    $('#edel').attr('href','#');
    $('#edel').attr('title','Not available in demo');

    $('.pdd').each(function(){
        $(this).attr('title','Delete(not available in demo)')
    });
    <? endif ?>
</script>