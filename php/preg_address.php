<?php
// PHP简单实现正则匹配省市区
$address = '广东省深圳市南山区';
preg_match('/(.*?(省|自治区|北京市|天津市))/', $address, $matches);
if (count($matches) > 1) {
  $province = $matches[count($matches) - 2];
  $address = str_replace($province, '', $address);
}
preg_match('/(.*?(市|自治州|地区|区划|县))/', $address, $matches);
if (count($matches) > 1) {
  $city = $matches[count($matches) - 2];
  $address = str_replace($city, '', $address);
}
preg_match('/(.*?(区|县|镇|乡|街道))/', $address, $matches);
if (count($matches) > 1) {
  $area = $matches[count($matches) - 2];
  $address = str_replace($area, '', $address);
}
return [
  'province' => isset($province) ? $province : '',
  'city' => isset($city) ? $city : '',
  'area' => isset($area) ? $area : '',
];
