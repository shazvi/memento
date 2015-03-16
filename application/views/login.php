<div class="row">
    <?php 
    echo validation_errors(); 
    echo form_open('page/login');
    ?>
        <fieldset>
            <div class="control-group">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input autofocus required id="box1" name="name" placeholder="Username" type="text" />
                </div>
                <span id=1 class="help-inline"></span>
            </div>
            <div class="control-group">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input required id="box2" name="pass" placeholder="Password" type="password" />
                </div>
                <span id=2 class="help-inline"></span>
            </div>
            <div class="control-group">
                <button type="submit" class="btn" id="btn">Log In</button>
            </div>
        </fieldset>
    </form>
</div>