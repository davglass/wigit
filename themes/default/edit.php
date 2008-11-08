
<div id="edit">
    <h2>Editing Page</h2>
    <p>Now editing: <a href="<?php echo($this->config['resource']['navurl']); ?>"><?php echo($this->config['resource']['title']); ?></a></p>
    <form method="post" action="<?php echo($this->config['url']) ?>">
        <textarea name="content" cols="100" rows="30"><?php echo($this->getContent()); ?></textarea><br>
        <input type="submit" name="save" value="Save">
    </form>

</div>
