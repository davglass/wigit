
<form method="post" action="<?php $this->config['url'] ?>">
<textarea name="content" cols="100" rows="30"><?php echo($this->getContent()); ?></textarea>
<input type="submit" name="save" value="Save">
</form>
