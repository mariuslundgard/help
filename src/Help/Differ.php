<?php

namespace Help;

use Closure;
use Exception;

class Differ
{
    protected $lineJunkCallback;
    protected $charJunkCallback;

    /**
    * Construct a text differencer, with optional filters.
    *
    * The two optional keyword parameters are for filter functions:
    *
    * - `lineJunkCallback`: A function that should accept a single string argument,
    *   and return true iff the string is junk. The module-level function
    *   `IS_LINE_JUNK` may be used to filter out lines without visible
    *   characters, except for at most one splat ('#').
    *
    * - `charJunkCallback`: A function that should accept a string of length 1. The
    *   module-level function `IS_CHARACTER_JUNK` may be used to filter out
    *   whitespace characters (a blank or tab; **note**: bad idea to include
    *   newline in this!).
    * */
    public function __construct(Closure $lineJunkCallback = null, Closure $charJunkCallback = null)
    {
        $this->lineJunkCallback = $lineJunkCallback;
        $this->charJunkCallback = $charJunkCallback;
    }


    public function encode($a, $b)
    {
        $cruncher = new SequenceMatcher($this->lineJunkCallback, $a, $b);

        $ret = [];
        $str = $a;

        d($a, $b);

        d('matching blocks', $cruncher->getMatchingBlocks());
        exit;

        // d($cruncher->getOpCodes());
        // exit;

        // foreach ($cruncher->getOpCodes() as $opCode) {
        //     list ($tag, $alo, $ahi, $blo, $bhi) = $opCode;

        //     d('op code', $opCode);

        //     // while ($index < ) {

        //     // }

        //     // $g = $this->_fancyReplace($a, $alo, $ahi, $b, $blo, $bhi);

        //     // echo $g;
        //     // print_r($opCode);
        //     // exit;

        //     // if ($tag === 'replace') {
        //         // $g = $this->_fancyReplace($a, $alo, $ahi, $b, $blo, $bhi);
        //         // d($g);
        //     // }
        //      // elseif ($tag == 'delete') {
        //     //     $g = $this->_dump('-', $a, $alo, $ahi);
        //     // } elseif ($tag == 'insert') {
        //     //     $g = $this->_dump('+', $b, $blo, $bhi);
        //     // } elseif ($tag == 'equal') {
        //     //     $g = $this->_dump(' ', $a, $alo, $ahi);
        //     // } else {
        //     //     throw new Exception('Unknown tag `' . $tag .'`');
        //     // }

        //     // foreach ($g as $line) {
        //     //     $ret[] = $line;
        //     // }
        // }
        // // exit;

        // return $ret;
    }

    public function compare($a, $b)
    {
        $cruncher = new SequenceMatcher($this->lineJunkCallback, $a, $b);

        $ret = [];

        foreach ($cruncher->getOpCodes() as $opCode) {
            list ($tag, $alo, $ahi, $blo, $bhi) = $opCode;

            if ($tag === 'replace') {
                $g = $this->_fancyReplace($a, $alo, $ahi, $b, $blo, $bhi);
            } elseif ($tag == 'delete') {
                $g = $this->_dump('-', $a, $alo, $ahi);
            } elseif ($tag == 'insert') {
                $g = $this->_dump('+', $b, $blo, $bhi);
            } elseif ($tag == 'equal') {
                $g = $this->_dump(' ', $a, $alo, $ahi);
            } else {
                throw new Exception('Unknown tag `' . $tag .'`');
            }

            foreach ($g as $line) {
                $ret[] = $line;
            }
        }

        return $ret;
    }

    /**
      * Generate comparison results for a same-tagged range.
      * 
      * @param type $tag 
      * @param type $x 
      * @param type $lo 
      * @param type $hi 
      * @return type
      */
    protected function _dump($tag, $x, $lo, $hi)
    {
        $ret = [];
        foreach (range($lo, $hi) as $index) {
            $ret[] = $tag . ' ' . @$x[$index];
        }

        return $ret;
    }

    /**
    * When replacing one block of lines with another, search the blocks
    * for *similar* lines; the best-matching pair (if any) is used as a
    * synch point, and intraline difference marking is done on the
    * similar pair. Lots of work, but often worth it.
    *
    * Example:
    *
    * >>> d = Differ()
    * >>> d._fancy_replace(['abcDefghiJkl\n'], 0, 1, ['abcdefGhijkl\n'], 0, 1)
    * >>> print ''.join(d.results),
    * - abcDefghiJkl
    * ?    ^  ^  ^
    * + abcdefGhijkl
    * ?    ^  ^  ^
    */
    protected function _fancyReplace($a, $alo, $ahi, $b, $blo, $bhi)
    {
        $ret = [];

        // don't synch up unless the lines have a similarity score of at
        // least cutoff; bestRatio tracks the best score seen so far
        list ($bestRatio, $cutoff) = [0.74, 0.75];

        $cruncher = new SequenceMatcher($this->charJunkCallback);

        $eqi = $eqj = null;

        // search for the pair that matches best without being identical
        // (identical lines must be junk lines, & we don't want to synch up
        // on junk -- unless we have to)
        foreach (range($blo, $bhi - 1) as $j) {

            $bj = $b[$j];
            $cruncher->setSequenceB($bj);

            foreach (range($alo, $ahi - 1) as $i) {
                $ai = $a[$i];

                if ($ai === $bj) {
                    if ($eqi === null) {
                        list ($eqi, $eqj) = [$i, $j];
                    }
                    continue;
                }

                $cruncher->setSequenceA($ai);

                // computing similarity is expensive, so use the quick
                // upper bounds first -- have seen this speed up messy
                // compares by a factor of 3.
                // note that ratio() is only expensive to compute the first
                // time it's called on a sequence pair; the expensive part
                // of the computation is cached by cruncher
                if ($cruncher->realQuickRatio() > $bestRatio && $cruncher->quickRatio() > $bestRatio && $cruncher->ratio() > $bestRatio) {
                    list ($bestRatio, $best_i, $best_j) = [$cruncher->ratio(), $i, $j];
                }
            }
        }

        if ($bestRatio < $cutoff) {
            
            // no non-identical "pretty close" pair
            if ($eqi === null) {
                
                // no identical pair either -- treat it as a straight replace
                foreach ($this->_plainReplace($a, $alo, $ahi, $b, $blo, $bhi) as $line) {
                    $ret[] = $line;
                }

                return $ret;
            }

            // no close pair, but an identical pair -- synch up on that
            list ($best_i, $best_j, $bestRatio) = [$eqi, $eqj, 1.0];

        } else {

            // there's a close pair, so forget the identical pair (if any)
            $eqi = null;
        }

        // pump out diffs from before the synch point        
        foreach ($this->_fancyHelper($a, $alo, $best_i, $b, $blo, $best_j) as $line) {
            $ret[] = $line;
        }

        // do intraline marking on the synch pair
        list ($aelt, $belt) = [$a[$best_i], $b[$best_j]];
        
        if ($eqi === null) {

            // pump out a '-', '?', '+', '?' quad for the synched lines
            $atags = $btags = '';
            $cruncher->setSequences($aelt, $belt);

            foreach ($cruncher->getOpCodes() as $opCode) {
                list ($tag, $ai1, $ai2, $bj1, $bj2) = $opCode;
                list ($la, $lb) = [$ai2 - $ai1, $bj2 - $bj1];
        
                if ($tag == 'replace') {
                    $atags += str_repeat('^', $la);
                    $btags += str_repeat('^', $lb);
                } elseif ($tag == 'delete') {
                    $atags += str_repeat('-', $la);
                } elseif ($tag == 'insert') {
                    $btags += str_repeat('+', $lb);;
                } elseif ($tag == 'equal') {
                    $atags += str_repeat(' ', $la);
                    $btags += str_repeat(' ', $lb);
                } else {
                    throw new Exception('unknown tag `' . $tag . '`');
                }
            }

            foreach ($this->_qFormat($aelt, $belt, $atags, $btags) as $line) {
                $ret[] = $line;
            }

        } else {
            $ret[] = '  ' . $aelt;
        }

        // pump out diffs from after the synch point
        foreach ($this->_fancyHelper($a, $best_i + 1, $ahi, $b, $best_j + 1, $bhi) as $line) {
            $ret[] = $line;
        }

        return $ret;
    }

    protected function _plainReplace($a, $alo, $ahi, $b, $blo, $bhi)
    {
        $ret = [];

        if ($alo < $ahi && $blo < $bhi) {
            // dump the shorter block first -- reduces the burden on short-term
            // memory if the blocks are of very different sizes
            if ($bhi - $blo < $ahi - $alo) {
                $first  = $this->_dump('+', $b, $blo, $bhi);
                $second = $this->_dump('-', $a, $alo, $ahi);
            } else {
                $first  = $this->_dump('-', $a, $alo, $ahi);
                $second = $this->_dump('+', $b, $blo, $bhi);
            }

            foreach ([$first, $second] as $g) {
            // for ($g in $first, $second:) {
                foreach ($g as $line) {
                    $ret[] = $line;
                }
            }
        }

        return $ret;
    }

    protected function _fancyHelper($a, $alo, $ahi, $b, $blo, $bhi)
    {
        $ret = [];
        $g = [];

        if ($alo < $ahi) {
            if ($blo < $bhi) {
                $g = $this->_fancyReplace($a, $alo, $ahi, $b, $blo, $bhi);
            } else {
                $g = $this->_dump('-', $a, $alo, $ahi);
            }
        } elseif ($blo < $bhi) {
            $g = $this->_dump('+', $b, $blo, $bhi);
        }

        foreach ($g as $line) {
            $ret[] = $line;
        }

        return $ret;
    }
}
