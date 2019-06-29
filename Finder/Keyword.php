<?php

namespace Keylink\Finder;

class Keyword {
	protected $searchKeywords = [];

	protected $numStates = 1;

	protected $outputs = [];

	protected $noTransitions = [];

	protected $yesTransitions = [];

	public function __construct(array $searchKeywords) {
		foreach ( $searchKeywords as $keyword ) {
			if ( $keyword !== '' ) {
				$this->searchKeywords[$keyword] = mb_strlen( $keyword );
			}
		}

		if ( !$this->searchKeywords ) {
			trigger_error( __METHOD__ . ': The set of search keywords is empty.', E_USER_WARNING );
			return;
		}

		$this->computeYesTransitions();
		$this->computeNoTransitions();
	}

	public function getKeywords() {
		return array_keys( $this->searchKeywords );
	}

	public function nextState( $currentState, $inputChar ) {
		$initialState = $currentState;
		while ( true ) {
			$transitions =& $this->yesTransitions[$currentState];
			if ( isset( $transitions[$inputChar] ) ) {
				$nextState = $transitions[$inputChar];
				if ( $currentState !== $initialState ) {
					$this->yesTransitions[$initialState][$inputChar] = $nextState;
				}
				return $nextState;
			}
			if ( $currentState === 0 ) {
				return 0;
			}
			$currentState = $this->noTransitions[$currentState];
		}

	}

	public function searchIn( $text ) {
		if ( !$this->searchKeywords || $text === '' ) {
			return [];  // fast path
		}

		$state = 0;
		$results = [];
		$length = mb_strlen( $text );

		for ( $i = 0; $i < $length; $i++ ) {
			$ch = mb_substr($text, $i, 1);
			$state = $this->nextState( $state, $ch );
			foreach ( $this->outputs[$state] as $match ) {
				$offset = $i - $this->searchKeywords[$match] + 1;
				$results[] = [ $offset, $match ];
			}
		}

		return $results;
	}

	protected function computeYesTransitions() {
		$this->yesTransitions = [ [] ];
		$this->outputs = [ [] ];
		foreach ( $this->searchKeywords as $keyword => $length ) {
			$state = 0;
			for ( $i = 0; $i < $length; $i++ ) {
				$ch = mb_substr($keyword, $i, 1);
				if ( !empty( $this->yesTransitions[$state][$ch] ) ) {
					$state = $this->yesTransitions[$state][$ch];
				} else {
					$this->yesTransitions[$state][$ch] = $this->numStates;
					$this->yesTransitions[] = [];
					$this->outputs[] = [];
					$state = $this->numStates++;
				}
			}

			$this->outputs[$state][] = $keyword;
		}
	}

	protected function computeNoTransitions() {
		$queue = [];
		$this->noTransitions = [];

		foreach ( $this->yesTransitions[0] as $ch => $toState ) {
			$queue[] = $toState;
			$this->noTransitions[$toState] = 0;
		}

		while ( true ) {
			$fromState = array_shift( $queue );
			if ( $fromState === null ) {
				break;
			}
			foreach ( $this->yesTransitions[$fromState] as $ch => $toState ) {
				$queue[] = $toState;
				$state = $this->noTransitions[$fromState];

				while ( $state !== 0 && empty( $this->yesTransitions[$state][$ch] ) ) {
					$state = $this->noTransitions[$state];
				}

				if ( isset( $this->yesTransitions[$state][$ch] ) ) {
					$noState = $this->yesTransitions[$state][$ch];
				} else {
					$noState = 0;
				}

				$this->noTransitions[$toState] = $noState;
				$this->outputs[$toState] = array_merge(
					$this->outputs[$toState], $this->outputs[$noState] );
			}
		}
	}
}
