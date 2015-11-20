<?php

   $detect = New \Addons\MobileDetect\MobileDetect();

   define('MOBILE', $detect->isMobile());
   define('TABLET', $detect->isTablet()); 