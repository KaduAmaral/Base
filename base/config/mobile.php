<?php

   $detect = New \Detection\MobileDetect();

   define('MOBILE', $detect->isMobile());
   define('TABLET', $detect->isTablet()); 