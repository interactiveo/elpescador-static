<?php if($field_fax_number = get_field('fax_number', 'options')):
  $field_fax_number_stripped = preg_replace('~\D~', '', $field_fax_number);
  echo '
  <div class="field--theme_fax">
  <a href="fax:'.$field_fax_number_stripped.'" title="Fax: '.$field_fax_number.'"><span class="icon-fax relative">'.$field_fax_number.'</span></a>
  </div>
  ';
endif; ?>
