<?php 
/**
 *  PHPCheckMate
 * 
 *  @author David Soyez <phpgnu@0x404.net>
 *  @version 1.0
 *  @copyright Copyright (c) 2009-2010, David Soyez
 *  @license GNU General Public License Version 3 <http://www.gnu.org/licenses/>
 *  @package phpcheckmate
 *  
 *  PHPCheckMate is a portage of Chess written in Perl by Brian Richardson
 *  Original Perl source can be find at http://search.cpan.org/~bjr/
 *  
 *  PHPCheckMate is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Foobar is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

  include_once('../Board.class.php');
  include_once('../Piece.class.php');

  class Bishop extends Piece {
  
  
  	function reachable_squares(){
  		#Array of square where the piece can move to
  		$squares = array();
  	
	    $csq 	= $this->get_current_square();
	    $hdist 	= abs(Board::horz_distance("a1", $csq));
	    $vdist 	= abs(Board::vert_distance("a1", $csq));
	    $dist 	= $hdist > $vdist ? $vdist : $hdist;
	    $sq 	= Board::add_horz_distance($csq, -$dist);
	    $sq 	= Board::add_vert_distance($sq, -$dist);
	    $squares = array_merge($squares, Board::squares_in_line($csq, $sq));
	    
	    $hdist = abs(Board::horz_distance("h1", $csq));
	    $vdist = abs(Board::vert_distance("h1", $csq));
	    $dist = $hdist > $vdist ? $vdist : $hdist;
	    $sq = Board::add_horz_distance($csq, $dist);
	    $sq = Board::add_vert_distance($sq, -$dist);
	    $squares = array_merge($squares, Board::squares_in_line($csq, $sq));
	    
	    $hdist = abs(Board::horz_distance("a8", $csq));
	    $vdist = abs(Board::vert_distance("a8", $csq));
	    $dist = $hdist > $vdist ? $vdist : $hdist;
	    $sq = Board::add_horz_distance($csq, -$dist);
	    $sq = Board::add_vert_distance($sq, $dist);
	    $squares = array_merge($squares, Board::squares_in_line($csq, $sq));
	    
	    $hdist = abs(Board::horz_distance("h8", $csq));
	    $vdist = abs(Board::vert_distance("h8", $csq));
	    $dist = $hdist > $vdist ? $vdist : $hdist;
	    $sq = Board::add_horz_distance($csq, $dist);
	    $sq = Board::add_vert_distance($sq, $dist);
	   	$squares = array_merge($squares, Board::squares_in_line($csq, $sq));
	    
	    #Removes current position square
	    $squares = preg_grep("/^$csq$/", $squares, PREG_GREP_INVERT);

	    return $squares;  	
  	}
  
  
  
  
  }
