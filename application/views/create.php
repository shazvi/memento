<div class="row">
    <?php 
    echo validation_errors();
    echo form_open('page/create');
    ?>
        <fieldset>
            <div class="control-group">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input autofocus name="name" placeholder="Username" type="text" required />
                </div>
                <span id=1 class="help-inline"></span>
            </div>
            <div class="control-group">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-envelope"></i></span>
                    <input name="email" placeholder="Email" type="email" required />
                </div>
                <span id=2 class="help-inline"></span>
            </div>
            <div class="control-group">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input name="pass" id="pass1" placeholder="Password" type="password" required/>
                </div>
            </div>
            <div class="control-group">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input name="conf" id="pass2" placeholder="Confirmation" type="password" required onkeyup="checkPass()"/>
                </div>
                <span id="confirmMessage" class="help-inline"></span>
            </div>
            <div class="control-group">
                <?php echo $image;?>
                    <div  class="wrapper"> <strong>Enter the letters in the image:</strong>
                        <input type="text" class="input" name="captcha">
                    </div>
            </div>
            <div class="control-group">
                <button type="submit" class="btn" id="btn">Register</button>
            </div>
        </fieldset>
    </form>
</div>