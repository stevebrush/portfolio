using UnityEngine;
using System.Collections;

public class UserInputHandler : MonoBehaviour {

	/*
	 * Sends out anonymous triggers.
	 * 
	 */

	private Camera mainCamera;
	private Vector2 startPosition;

	public delegate void TapAction(Touch t); // This is a reference to any method that accepts a Touch and returns nothing.
	public static event TapAction OnTap; // A message sent to other objects who've registered for the event. (it's static, so we can reference the event without instantiating the class)

	public float tapMaxMovement = 50f; // Maximum amount of movement in pixels that an input can still be considered a tap.
	private Vector2 movement; // Keeps track of how far we've moved.
	private bool tapGestureFailed = false; // Will determine if the touch gesture failed to be a tap.

	public delegate void MouseClickedAction(Vector2 position);
	public static event MouseClickedAction OnMouseClicked;

	public delegate void SwipeBeganAction(Touch t);
	public static event SwipeBeganAction OnSwipeBegan;

	public delegate void SwipeEndedAction(string direction);
	public static event SwipeEndedAction OnSwipeEnded;

	public delegate void DirectionalKeyPressed(string direction);
	public static event DirectionalKeyPressed OnDirectionalKeyPressed;

	private bool swipeGestureRecognized = false;
	private string swipeDirection;
	private Vector2 swipeSum;

	// Use this for initialization
	void Start () {
		mainCamera = Camera.main;
	}
	
	// Update is called once per frame
	void Update () {

		if (Input.GetMouseButtonDown (0)) {
			Vector2 position = mainCamera.ScreenToWorldPoint(Input.mousePosition);
			if (OnMouseClicked != null) {
				OnMouseClicked(position);
			}
		}

		else if (Input.GetKeyDown("left")) {
			if (OnDirectionalKeyPressed != null) {
				OnDirectionalKeyPressed("left");
			}

		} else if (Input.GetKeyDown("right")) {
			if (OnDirectionalKeyPressed != null) {
				OnDirectionalKeyPressed("right");
			}

		} else if (Input.GetKeyDown("down")) {
			if (OnDirectionalKeyPressed != null) {
				OnDirectionalKeyPressed("down");
			}

		} else if (Input.GetKeyDown("up")) {
			if (OnDirectionalKeyPressed != null) {
				OnDirectionalKeyPressed("up");
			}
		}


		// Touches
		if (Input.touchCount > 0) {

			Touch touch = Input.touches[0]; // Access first touch.

			/*
			 * Possible TouchPhase:
			 * Began, Moved, Stationary, Ended, Canceled
			 */

			if (touch.phase == TouchPhase.Began) {

				movement = Vector2.zero; // Set the movement of the touch to zero.
				startPosition = touch.position;

			} else if (touch.phase == TouchPhase.Moved || touch.phase == TouchPhase.Stationary) {

				movement += touch.deltaPosition; // Track how far the touch has moved since the last reading.

				if (!swipeGestureRecognized && movement.magnitude > tapMaxMovement) {

					swipeGestureRecognized = true;
					tapGestureFailed = true;

					if (OnSwipeBegan != null) {
						OnSwipeBegan(touch);
					}
				}

			} else {

				if (swipeGestureRecognized) {

					// Get the sum of the start and end points.
					swipeSum = touch.position - startPosition; 

					// Determine swipe's direction.
					if (Mathf.Abs(swipeSum.x) > Mathf.Abs(swipeSum.y)) {

						// Swipe is horizontal
						if (touch.position.x > startPosition.x) {
							swipeDirection = "right";
						} else {
							swipeDirection = "left";
						}

					} else {

						// Swipe is vertical
						if (touch.position.y > startPosition.y) {
							swipeDirection = "up";
						} else {
							swipeDirection = "down";
						}
					}

					if (OnSwipeEnded != null) {
						OnSwipeEnded(swipeDirection);
					}

				} else if (!tapGestureFailed) {
					if (OnTap != null) {
						OnTap(touch); // Process our touch event
					}
				}

				swipeGestureRecognized = false;
				tapGestureFailed = false;

			}
		}
	}
}
