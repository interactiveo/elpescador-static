<?php if($footer_copyright_text = get_field('footer_copyright_text', 'options')):
  echo '
  <div class="field--footer_copyright_text">
  '.$footer_copyright_text.'
  </div>
  ';
endif; ?>
