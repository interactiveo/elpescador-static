<?php if($field_phone_number = get_field('phone_number', 'options')):
  $field_phone_number_stripped = preg_replace('~\D~', '', $field_phone_number);
  echo '
  <div class="field--theme_phone">
  <a href="tel:'.$field_phone_number_stripped.'" title="Phone: '.$field_phone_number.'"><span>'.$field_phone_number.'</span></a>
  </div>
  ';
endif; ?>

<?php if($field_phone_number2 = get_field('phone_number2', 'options')):
  $field_phone_number_stripped2 = preg_replace('~\D~', '', $field_phone_number2);
  echo '
  <div class="field--theme_phone alt-phone">
  Reservations: <a href="tel:'.$field_phone_number_stripped2.'" title="Phone: '.$field_phone_number2.'"><span>'.$field_phone_number2.'</span></a>
  </div>
  ';
endif; ?>
