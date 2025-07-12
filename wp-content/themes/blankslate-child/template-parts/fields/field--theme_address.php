<?php if($field_street_address = get_field('street_address', 'options')):
  echo '
  <div class="field--theme_address">
  <address>
  '.$field_street_address.'
  </address>
  </div>
  ';
endif; ?>
