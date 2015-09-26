<?php

/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

namespace voku\helper\shim;

/**
 * Normalizer is a PHP fallback implementation of the Normalizer class provided by the intl extension.
 *
 * It has been validated with Unicode 6.3 Normalization Conformance Test.
 * See http://www.unicode.org/reports/tr15/ for detailed info about Unicode normalizations.
 *
 * @package voku\helper\shim
 */
class Normalizer
{
  const NONE    = 1;
  const FORM_D  = 2;
  const NFD     = 2;
  const FORM_KD = 3;
  const NFKD    = 3;
  const FORM_C  = 4;
  const NFC     = 4;
  const FORM_KC = 5;
  const NFKC    = 5;

  protected static $C, $D, $KD, $cC;

  /**
   * @var array
   */
  protected static $ulen_mask = array(
      "\xC0" => 2,
      "\xD0" => 2,
      "\xE0" => 3,
      "\xF0" => 4,
  );

  /**
   * @var string
   */
  protected static $ASCII = "\x20\x65\x69\x61\x73\x6E\x74\x72\x6F\x6C\x75\x64\x5D\x5B\x63\x6D\x70\x27\x0A\x67\x7C\x68\x76\x2E\x66\x62\x2C\x3A\x3D\x2D\x71\x31\x30\x43\x32\x2A\x79\x78\x29\x28\x4C\x39\x41\x53\x2F\x50\x22\x45\x6A\x4D\x49\x6B\x33\x3E\x35\x54\x3C\x44\x34\x7D\x42\x7B\x38\x46\x77\x52\x36\x37\x55\x47\x4E\x3B\x4A\x7A\x56\x23\x48\x4F\x57\x5F\x26\x21\x4B\x3F\x58\x51\x25\x59\x5C\x09\x5A\x2B\x7E\x5E\x24\x40\x60\x7F\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F";

  /**
   * is normalized
   *
   * @param string $str
   * @param int    $form
   *
   * @return bool
   */
  public static function isNormalized($str, $form = self::NFC)
  {
    if (strspn($str .= '', self::$ASCII) === strlen($str)) {
      return true;
    }

    if (
        self::NFC === $form
        &&
        preg_match('//u', $str)
        &&
        !preg_match('/[^\x00-\x{2FF}]/u', $str)
    ) {
      return true;
    }

    return false; // Pretend false as quick checks implemented in PHP won't be so quick
  }

  /**
   * normalize
   *
   * @param string $str
   * @param int    $form
   *
   * @return false|string false on error
   */
  public static function normalize($str, $form = self::NFC)
  {
    if (!preg_match('//u', $str .= '')) {
      return false;
    }

    switch ($form) {
      case self::NONE:
        return $str;
      case self::NFC:
        $C = true;
        $K = false;
        break;
      case self::NFD:
        $C = false;
        $K = false;
        break;
      case self::NFKC:
        $C = true;
        $K = true;
        break;
      case self::NFKD:
        $C = false;
        $K = true;
        break;
      default:
        return false;
    }

    if ('' === $str) {
      return '';
    }

    if ($K && empty(self::$KD)) {
      self::$KD = static::getData('compatibilityDecomposition');
    }

    if (empty(self::$D)) {
      self::$D = static::getData('canonicalDecomposition');
      self::$cC = static::getData('combiningClass');
    }

    if ($C) {
      if (empty(self::$C)) {
        self::$C = static::getData('canonicalComposition');
      }

      return self::recompose(self::decompose($str, $K));
    } else {
      return self::decompose($str, $K);
    }
  }

  /**
   * getData
   *
   * @param string $file
   *
   * @return bool|mixed
   */
  protected static function getData($file)
  {
    $file = __DIR__ . '/unidata/' . $file . '.ser';
    if (file_exists($file)) {
      return unserialize(file_get_contents($file));
    } else {
      return false;
    }
  }

  /**
   * recompose
   *
   * @param string $s
   *
   * @return string
   */
  protected static function recompose($s)
  {
    $ASCII = self::$ASCII;
    $compMap = self::$C;
    $combClass = self::$cC;
    $ulen_mask = self::$ulen_mask;

    $result = $tail = '';

    $i = $s[0] < "\x80" ? 1 : $ulen_mask[$s[0] & "\xF0"];
    $len = strlen($s);

    $last_uchr = substr($s, 0, $i);
    $last_ucls = isset($combClass[$last_uchr]) ? 256 : 0;

    while ($i < $len) {
      if ($s[$i] < "\x80") {
        // ASCII chars

        if ($tail) {
          $last_uchr .= $tail;
          $tail = '';
        }

        $j = strspn($s, $ASCII, $i + 1);

        if ($j) {
          $last_uchr .= substr($s, $i, $j);
          $i += $j;
        }

        $result .= $last_uchr;
        $last_uchr = $s[$i];
        ++$i;
      } else {
        $ulen = $ulen_mask[$s[$i] & "\xF0"];
        $uchr = substr($s, $i, $ulen);

        if (
            $last_uchr < "\xE1\x84\x80"
            ||
            "\xE1\x84\x92" < $last_uchr
            ||
            $uchr < "\xE1\x85\xA1"
            ||
            "\xE1\x85\xB5" < $uchr
            ||
            $last_ucls
        ) {
          // Table lookup and combining chars composition

          $ucls = isset($combClass[$uchr]) ? $combClass[$uchr] : 0;

          if (isset($compMap[$last_uchr . $uchr]) && (!$last_ucls || $last_ucls < $ucls)) {
            $last_uchr = $compMap[$last_uchr . $uchr];
          } elseif ($last_ucls = $ucls) { // this "=" isn't a typo
            $tail .= $uchr;
          } else {
            if ($tail) {
              $last_uchr .= $tail;
              $tail = '';
            }

            $result .= $last_uchr;
            $last_uchr = $uchr;
          }
        } else {
          // Hangul chars

          $L = ord($last_uchr[2]) - 0x80;
          $V = ord($uchr[2]) - 0xA1;
          $T = 0;

          $uchr = substr($s, $i + $ulen, 3);

          if ("\xE1\x86\xA7" <= $uchr && $uchr <= "\xE1\x87\x82") {
            $T = ord($uchr[2]) - 0xA7;
            0 > $T && $T += 0x40;
            $ulen += 3;
          }

          $L = 0xAC00 + ($L * 21 + $V) * 28 + $T;
          $last_uchr = chr(0xE0 | $L >> 12) . chr(0x80 | $L >> 6 & 0x3F) . chr(0x80 | $L & 0x3F);
        }

        $i += $ulen;
      }
    }

    $result = $result . $last_uchr . $tail;

    return $result;
  }

  /**
   * decompose
   *
   * @param string $s
   * @param bool   $c
   *
   * @return string
   */
  protected static function decompose($s, $c)
  {
    $result = '';

    $ASCII = self::$ASCII;
    $decompMap = self::$D;
    $combClass = self::$cC;
    $ulen_mask = self::$ulen_mask;

    if ($c) {
      $compatMap = self::$KD;
    }

    // init
    $c = array();
    $i = 0;
    $len = strlen($s);

    while ($i < $len) {
      if ($s[$i] < "\x80") {
        // ASCII chars

        if ($c) {
          ksort($c);
          $result .= implode('', $c);
          $c = array();
        }

        $j = 1 + strspn($s, $ASCII, $i + 1);
        $result .= substr($s, $i, $j);
        $i += $j;
      } else {
        $ulen = $ulen_mask[$s[$i] & "\xF0"];
        $uchr = substr($s, $i, $ulen);
        $i += $ulen;

        if (isset($combClass[$uchr])) {
          // Combining chars, for sorting

          isset($c[$combClass[$uchr]]) || $c[$combClass[$uchr]] = '';

          if (isset($compatMap[$uchr])) {
            $c[$combClass[$uchr]] .= $compatMap[$uchr];
          } else {
            $c[$combClass[$uchr]] .= (isset($decompMap[$uchr]) ? $decompMap[$uchr] : $uchr);
          }

        } else {

          if ($c) {
            ksort($c);
            $result .= implode('', $c);
            $c = array();
          }

          if ($uchr < "\xEA\xB0\x80" || "\xED\x9E\xA3" < $uchr) {
            // Table lookup

            if (isset($compatMap[$uchr])) {
              $j = $compatMap[$uchr];
            } else {
              $j = (isset($decompMap[$uchr]) ? $decompMap[$uchr] : $uchr);
            }

            if ($uchr != $j) {
              $uchr = $j;

              $j = strlen($uchr);
              $ulen = $uchr[0] < "\x80" ? 1 : $ulen_mask[$uchr[0] & "\xF0"];

              if ($ulen != $j) {
                // Put trailing chars in $s

                $j -= $ulen;
                $i -= $j;

                if (0 > $i) {
                  $s = str_repeat(' ', -$i) . $s;
                  $len -= $i;
                  $i = 0;
                }

                while ($j--) {
                  $s[$i + $j] = $uchr[$ulen + $j];
                }

                $uchr = substr($uchr, 0, $ulen);
              }
            }

          } else {
            // Hangul chars

            $uchr = unpack('C*', $uchr);
            $j = (($uchr[1] - 224) << 12) + (($uchr[2] - 128) << 6) + $uchr[3] - 0xAC80;

            $uchr = "\xE1\x84" . chr(0x80 + (int)($j / 588)) . "\xE1\x85" . chr(0xA1 + (int)(($j % 588) / 28));

            $j = $j % 28;

            if ($j) {
              $uchr .= $j < 25 ? ("\xE1\x86" . chr(0xA7 + $j)) : ("\xE1\x87" . chr(0x67 + $j));
            }
          }

          $result .= $uchr;
        }
      }
    }

    if ($c) {
      ksort($c);
      $result .= implode('', $c);
    }

    return $result;
  }
}
