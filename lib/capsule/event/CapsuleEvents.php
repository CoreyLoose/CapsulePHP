<?php
namespace org\capsule\event;

class CapsuleEvents
{
	/**
	 * Dispatched after the Capsule object has been setup
	 * but before the first call is made
	 */
	const Ready = 'Capsule.Ready';
	
	/**
	 * Dispatched immediatly before Capsule makes a url call
	 * 
	 * @param string &$url - The url to be called
	 * @param array &$get - The get parameters being used
	 * @param array &$post - The post parameters being used 
	 */
	const NewCall = 'Capsule.NewCall';
	
	/**
	 * Dispatched immediatly after Capsule makes a url call
	 * 
	 * @param scalar &$output - The results of the call
	 * @param string $url - The url that was called
	 * @param array &$get - The get parameters that were used
	 * @param array &$post - The post parameters that were used 
	 */
	const FinishedCall = 'Capsule.FinishedCall';
	
	/**
	 * Dispatched after the last Capsule call is made
	 * 
	 * @param scalar &$output - The final output
	 */
	const Finished = 'Capsule.Finished';
}