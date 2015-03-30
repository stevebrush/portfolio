using UnityEngine;
using System.Collections;

public class ContainerController : MonoBehaviour {

	// Events and delegates.
	public delegate void ContainerReady ();
	public event ContainerReady OnContainerReady;

	// Room.
	public GameObject room;
	private RoomController roomScript;

	// Sector.
	public GameObject sector;
	private Transform sectorTransform;

	// This container's sprite renderer.
	private SpriteRenderer sprite;
	private FadeInOut fadeInOut;

	// Layers
	private int inactiveLayer;
	private int activeLayer;
	public bool isFound = true;
	
	void Start () {

		// Set components.
		sprite = GetComponent<SpriteRenderer> ();
		roomScript = room.GetComponent<RoomController> ();
		fadeInOut = GetComponent<FadeInOut> ();

		// Presets.
		sprite.sortingOrder = 1;
		inactiveLayer = LayerMask.NameToLayer ("Default");
		activeLayer = LayerMask.NameToLayer ("ActiveItems");

		// Let's do this.
		fadeInOut.Hide ();
		PositionContainer ();
		Subscribe ();
		ShowContainer ();
	}

	void OnDisable () {
		Unsubscribe ();
	}

	private void ActivateContainer() {
		gameObject.layer = activeLayer;
	}
	private void DeactivateContainer() {
		gameObject.layer = inactiveLayer;
	}

	private void PositionContainer () {}

	private void CheckReady () {
		if (OnContainerReady != null) {
			OnContainerReady ();
		}
	}

	private void HideContainer () {
		fadeInOut.FadeOut ("fast");
	}

	public void MakeFound() {
		isFound = true;
		roomScript.roomReady = true; // Once this container fades in, we'll need to reactivate the room.
		fadeInOut.FadeIn ("slow");
	}

	public void ShowContainer () {

		if (roomScript.selected == false) {
			return;
		}

		ActivateContainer ();

		if (isFound == false) {
			CheckReady ();
			return;
		}

		if (roomScript.visited) {
			fadeInOut.FadeIn ("fast");
		} else {
			fadeInOut.FadeIn ("slow");
		}
	}

	private void Subscribe () {
		roomScript.OnEnter += ShowContainer;
		fadeInOut.OnFadeInComplete += CheckReady;
		roomScript.OnExit += DeactivateContainer;
		roomScript.OnExit += HideContainer;

	}

	private void Unsubscribe () {
		if (roomScript != null) {
			roomScript.OnEnter -= ShowContainer;
			roomScript.OnExit -= DeactivateContainer;
			roomScript.OnExit -= HideContainer;
		}
		if (fadeInOut != null) {
			fadeInOut.OnFadeInComplete -= CheckReady;
		}
	}
}
