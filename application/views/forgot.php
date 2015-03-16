<?php echo validation_errors(); ?>

<?php echo form_open('page/forgot') ?>
    <fieldset>
        <div class="control-group">
        	<div class="input-prepend">
                <span class="add-on"><i class="icon-envelope"></i></span>
            	<input autofocus name="email" placeholder="Email Address" type="text" required/>
            </div>
        </div>
        <div class="control-group">
            <button type="submit" class="btn">Send New Password</button>
        </div>
    </fieldset>
</form>