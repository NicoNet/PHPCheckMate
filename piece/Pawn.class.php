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

  include_once('Board.class.php');
  include_once('Piece.class.php');

  class Pawn extends Piece {
  
  
  	function reachable_squares(){
  		#Array of square where the piece can move to
  		$squares 	= array();
  		$color 		= $this->get_player();
  		$csq 		= $this->get_current_square();
  	
	    if ($color == 'white') {
			if($csq)
				$tsq1 = Board::square_up_from($csq) ;
	    }
	    else {
			if($csq)
				$tsq1 = Board::square_down_from($csq) ;
	    }
	    if(isset($tsq1))
	    	$squares[] = $tsq1 ;

	    if ($color == 'white') {
			if($tsq1)
				$tsq2 = Board::square_up_from($tsq1) ;
	    }
	    else {
			if($tsq1)
				$tsq2 = Board::square_down_from($tsq1) ;
	    }
	    
	    if(!$this->moved() and $tsq2)
	    	$squares[] = $tsq2 ;
	    	
	    if($tsq1)
	    	$tsq2 = Board::square_left_of($tsq1) ;
	    	
	    if($tsq2)
	    	$squares[] = $tsq2 ;
	    	
	    if($tsq1)
	    	$tsq2 = Board::square_right_of($tsq1) ;
	    	
	    if($tsq2)
	    	$squares[] = $tsq2 ;

	    return $squares;  	
  	}
  
  	
  	
  	
  	/**
  	 * Promote the Pawn by return a new $new_rank object subclass of Piece and set
  	 * the same properties values as the Pawn instance had.
  	 * 
  	 * @param string $new_rank subclassname of Piece (bishop, knight, rook or queen)
  	 * @return Piece the piece instance created
  	 */
  	
  	function promote($new_rank) {
  	
  	#Checj if can promote pawn to $new_rank
    if(strtolower($new_rank) != 'bishop' && strtolower($new_rank) != 'knight' &&
       strtolower($new_rank) != 'rook'   && strtolower($new_rank) != 'queen') {
		return NULL;
    }
    
    #Create the newpiece
    $newpiecelass	= ucfirst($new_rank);
    $newPiece 		= new $newpiecelass($this->get_current_square(), $this->get_color());
       
    #Copy Piece properties of the pawn to the newpiece
    $reflection = new ReflectionObject($this);
    $properties = $reflection->getProperties();
    foreach($properties as $propertyObj){
	    $pName 	= $propertyObj->getName();
		$pVal	= $propertyObj->getValue($this);
		$pclass	= $reflection->getProperty($pName)->class;

		if($pclass == 'Piece'){
			$newPiece->$pName	= $pVal;
		}
    }   

    return $newPiece;
}
  
  
  
  }
