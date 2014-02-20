<?php

namespace Help;

use Closure;

class SequenceMatcher
{
    protected $isJunkCallback;
    protected $a;
    protected $b;
    protected $matchingBlocks;
    protected $opCodes;
    protected $fullBCount;
    protected $isBJunk;
    protected $b2j;

    public function __construct(Closure $isJunkCallback = null, $a = '', $b = '')
    {
        $this->isJunkCallback = $isJunkCallback;
        $this->setSequences($a, $b);
    }

    public function setSequences($a, $b)
    {
        $this->setSequenceA($a);
        $this->setSequenceB($b);
    }

    public function setSequenceA($a)
    {
        if ($a !== $this->a) {
            $this->a = $a;

            // reset blocks and op codes
            $this->matchingBlocks = $this->opCodes = null;
        }
    }

    public function setSequenceB($b)
    {
        if ($b !== $this->b) {
                
            $this->b = $b;

            // reset blocks and op codes
            $this->matchingBlocks = $this->opCodes = null;

            // reset full b count
            $this->fullBCount = null;

            // chain
            $this->_chainB();
        }
    }

    public function getOpCodes()
    {
        if (null === $this->opCodes) {
            $i = $j = 0;
            $this->opCodes = [];

            foreach ($this->getMatchingBlocks() as $block) {
                list ($ai, $bj, $size) = $block;

                // invariant:  we've pumped out correct diffs to change
                // a[:i] into b[:j], and the next matching block is
                // a[ai:ai+size] == b[bj:bj+size].  So we need to pump
                // out a diff to change a[i:ai] into b[j:bj], pump out
                // the matching block, and move (i,j) beyond the match
                $tag = null;

                //
                if ($i < $ai && $j < $bj) {
                    $tag = 'replace';
                } elseif ($i < $ai) {
                    $tag = 'delete';
                } elseif ($j < $bj) {
                    $tag = 'insert';
                }

                if ($tag) {
                    $this->opCodes[] = [$tag, $i, $ai, $j, $bj];
                }

                $i = $ai + $size;
                $j = $bj + $size;

                // the list of matching blocks is terminated by a
                // sentinel with size 0
                if ($size) {
                    $this->opCodes[] = ['equal', $ai, $i, $bj, $j];
                }
            }
        }

        return $this->opCodes;
    }

    public function getMatchingBlocks()
    {
        if (null === $this->matchingBlocks) {
            $this->matchingBlocks = [];

            $la = strlen($this->a);
            $lb = strlen($this->b);
            
            $this->_helper(0, $la, 0, $lb, $this->matchingBlocks);

            $this->matchingBlocks[] = [$la, $lb, 0];
        }

        return $this->matchingBlocks;
    }

    protected function _helper($alo, $ahi, $blo, $bhi, array &$blocks)
    {
        $x = $this->findLongestMatch($alo, $ahi, $blo, $bhi);

        list ($i, $j, $k) = $x;

        if ($k) {
            if ($alo < $i && $blo < $j) {
                $this->_helper($alo, $i, $blo, $j, $blocks);
            }

            $blocks[] = $lm;

            if ($i + $k < $ahi && $j + $k < $bhi) {
                $this->_helper($i + $k, $ahi, $j + $k, $bhi, $blocks);
            }
        }
    }

    public function findLongestMatch($alo, $ahi, $blo, $bhi)
    {
        $besti = $alo;
        $bestj = $blo;
        $bestsize = 0;

        // find longest junk-free match
        // during an iteration of the loop, j2len[j] = length of longest
        // junk-free match ending with a[i-1] and b[j]
        $j2len = $nothing = [];

        foreach (range($alo, $ahi - 1) as $index) {
            // look at all instances of a[i] in b; note that because
            // b2j has no junk keys, the loop is skipped if a[i] is junk

            $newj2len = [];

            $j2lenGet = function($key, $default) use (&$j2len) {
                return isset($j2len[$key]) ? $j2len[$key] : $default;
            };

            foreach ($j2lenGet($this->a[$index], $nothing) as $j) {
                // a[i] matches b[j]
                if ($j < $blo) {
                    continue;
                }

                if ($j >= $bhi) {
                    break;
                }

                $k = $newj2len[$j] = $j2lenGet($j-1, 0) + 1;

                if ($k > $bestsize) {
                    $besti = $i - $k + 1;
                    $bestj = $j - $k + 1;
                    $bestsize = $k;
                }
            }

            $j2len = $newj2len;
        }

        // // # Now that we have a wholly interesting match (albeit possibly
        // // # empty!), we may as well suck up the matching junk on each
        // // # side of it too.  Can't think of a good reason not to, and it
        // // # saves post-processing the (possibly considerable) expense of
        // // # figuring out what to do with it.  In the case of an empty
        // // # interesting match, this is clearly the right thing to do,
        // // # because no other kind of match is possible in the regions.
        // while ($besti > $alo && $bestj > $blo && $this->isBJunk($b[$bestj-1]) && $a[$besti - 1] == $b[$bestj-1]) {
        //     $besti = $besti - 1;
        //     $bestj = $bestj - 1;
        //     $bestSize = $bestSize + 1;
        // }

        // while ($besti + $bestSize < $ahi && $bestj + $bestSize < $bhi && $this->isBJunk($b[$bestj + $bestSize]) && $a[$besti + $bestSize] == $b[$bestj + $bestSize]) {
        //     $bestSize = $bestSize + 1;
        // }

        // return [$besti, $bestj, $bestSize];
        // // return besti, bestj, bestSize

        $isBJunk = $this->isBJunk;

        while ($besti > $alo && $bestj > $blo && $isBJunk($this->b[$bestj-1]) && $this->a[$besti-1] == $this->b[$bestj-1]) {
            list($besti, $bestj, $bestsize) = [$besti - 1, $bestj - 1, $bestsize + 1];
        }

        while ($besti+$bestsize < $ahi && $bestj+$bestsize < $bhi && $isBJunk($this->b[$bestj+$bestsize]) && $this->a[$besti+$bestsize] == $this->b[$bestj+$bestsize]) {
            $bestsize = $bestsize + 1;
        }

        return [$besti, $bestj, $bestsize];
    }

    protected function _chainB()
    {
        // $b = $this->b;
        
        $this->b2j = [];

        $len = strlen($this->b);
        $index = 0;


        // foreach (range(0, $len - 1) as $index) {
        while ($index < $len) {
            $char = $this->b[$index];
            // echo json_encode($this->b2j) . '<br>';
            // echo $char;
            if (isset($this->b2j[$char])) {
                $this->b2j[$char][] = $index;
            } else {
                $this->b2j[$char] = [$index];
            }
            $index++;
        }

        // d($this->b2j);
        // exit;

        // $isJunk = 
        $junkDict = [];
        // isJunkCallback, junkdict = $this->isJunkCallback, {}
        if ($this->isJunkCallback) {
            foreach ($this->b2j as $char => $x) {

        //     for char in this->b2j.keys():
                if (call_user_func_array($this->isJunkCallback, [$char])) {
        //         if isJunkCallback(char):
                    $junkdict[$char] = true;
        //             junkdict[char] = 1   # value irrelevant; it's a set
                    unset($this->b2j[$char]);
        //             del this->b2j[char]
                }
            }
        }

        // isjunk, junkdict = self.isjunk, {}
        // if isjunk:
        //     for elt in b2j.keys():
        //         if isjunk(elt):
        //             junkdict[elt] = 1   # value irrelevant; it's a set
        //             del b2j[elt]

        // d($this->b2j);
        // exit;

        // isjunk, junkdict = self.isjunk, {}
        // if isjunk:
        //     for elt in b2j.keys():
        //         if isjunk(elt):
        //             junkdict[elt] = 1   # value irrelevant; it's a set
        //             del b2j[elt]

        # Now for x in b, isJunkCallback(x) == junkdict.has_key(x), but the
        # latter is much faster.  Note too that while there may be a
        # lot of junk in the sequence, the number of *unique* junk
        # elements is probably small.  So the memory burden of keeping
        # this dict alive is likely trivial compared to the size of b2j.
        // $this->isbjunk = junkdict.has_key
        // $this->b2j = $b2j;
        $this->isBJunk = function ($key) use ($junkDict) {
            return isset($junkDict[$key]);
        };
        // exit;

        // d($this);
    }

    public function realQuickRatio()
    {
        list ($la, $lb) = [strlen($this->a), strlen($this->b)];
        // can't have more matches than the number of elements in the
        // shorter sequence
        return 2.0 * min($la, $lb) / ($la + $lb);
    }

    public function quickRatio()
    {
        // viewing a and b as multisets, set matches to the cardinality
        // of their intersection; this counts the number of matches
        // without regard to order, so is clearly an upper bound
        if ($this->fullBCount === null) {
            $this->fullBCount = [];

            $index = 0;
            $len = strlen($this->b);

            while ($index < $len) {
            //     // echo $index;
                $elt = $this->b[$index];
                $this->fullBCount[$elt] = (isset($this->fullBCount[$elt]) ? $this->fullBCount[$elt] : 0) + 1;
                $index++;
            }
            // d($index, $len);
            // exit;
        }

        // avail[x] is the number of times x appears in 'b' less the
        // number of times we've seen it in 'a' so far ... kinda
        $avail = [];
        $availhas = function ($key) use ($avail) {
            return isset($avail[$key]);
        };

        $matches = 0;
        $index = 0;
        $len = strlen($this->b);

        while ($index < $len) {
            $elt = $this->b[$index];
        
            if ($availhas($elt)) {
                $numb = $avail[$elt];
            } else {
                $numb = isset($this->fullBCount[$elt]) ? $this->fullBCount[$elt] : 0;
            }

            $avail[$elt] = $numb - 1;

            if ($numb > 0) {
                $matches = $matches + 1;
            }

            $index++;
        }

        return 2.0 * $matches / (strlen($this->a) + strlen($this->b));
    }

    public function ratio()
    {
        $sum = 0;
        $calc = function($sum, $triple) use ($sum) {
            $sum += ($sum + ($triple - 1));
        };

        $matches = $calc($this->getMatchingBlocks(), 0);

        // reduce(lambda sum, triple: sum + triple[-1],
        //                  self.get_matching_blocks(), 0)
        // $matches = static::_reduce(function ($a, $b) { return $a + $b; }, triple: sum + triple[-1],
        //                  self.get_matching_blocks(), 0)

        return 2.0 * $matches / (strlen($this->a) + strlen($this->b));
    }
}
