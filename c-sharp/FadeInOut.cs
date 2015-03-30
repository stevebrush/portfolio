using UnityEngine;
using System.Collections;

public class FadeInOut : MonoBehaviour {

	public delegate void FadeComplete();
	public event FadeComplete OnFadeOutComplete;
	public event FadeComplete OnFadeInComplete;

	private Color color;
	private float waitTime = 0.001f;
	//private float[] iterators = {0.1f, 0.1f, 0.1f};
	private float[] iterators = {0.01f, 0.08f, 0.1f};

	bool isRunning = false;

	void Awake() {
		color = renderer.material.color;
	}

	public void Hide() {
		SetOpacity (0.0f);
	}

	public void SetOpacity(float opacity) {
		if (isRunning) {
			return;
		}
		color.a = opacity;
		renderer.material.color = color;
	}

	public void FadeIn(string speed = "slow") {
		float opacity = 1.0f;
		if (opacity == color.a) {
			Debug.Log ("The opacity is the same; exit!");
			if (OnFadeInComplete != null) {
				OnFadeInComplete ();
			}
			return;
		}
		StartCoroutine (DoFadeIn (opacity, GetSpeed(speed)));
	}

	public void FadeOut(string speed = "slow") {
		float opacity = 0.0f;
		if (opacity == color.a) {
			if (OnFadeOutComplete != null) {
				OnFadeOutComplete ();
			}
			return;
		}
		StartCoroutine (DoFadeOut (opacity, GetSpeed(speed)));
	}

	public void FadeAuto(float opacity, string speed = "slow") {

		// Fade out
		if (opacity < color.a) {
			StartCoroutine (DoFadeOut (opacity, GetSpeed(speed)));
		} 
		
		// Fade in
		else {
			StartCoroutine (DoFadeIn (opacity, GetSpeed(speed)));
		}
	}

	private float GetSpeed(string speed) {
		float speedFloat;
		switch (speed) {
		case "slow":
			speedFloat = iterators[0];
			break;
		case "medium":
		default:
			speedFloat = iterators[1];
			break;
		case "fast":
			speedFloat = iterators[2];
			break;
		}
		return speedFloat;
	}

	public void Flicker(float time) {
		StartCoroutine (DoFlicker (time));
	}

	IEnumerator DoFadeOut(float opacity, float speed) {
		if (isRunning) {
			return false;
		}
		isRunning = true;
		while (color.a >= opacity) {
			color.a -= speed;
			renderer.material.color = color;
			yield return new WaitForSeconds (waitTime);
		}
		color.a = opacity;
		renderer.material.color = color;

		if (OnFadeOutComplete != null) {
			OnFadeOutComplete ();
		}
		isRunning = false;
	}

	IEnumerator DoFadeIn(float opacity, float speed) {
		if (isRunning) {
			return false;
		}
		isRunning = true;
		while (color.a <= opacity) {
			color.a += speed;
			renderer.material.color = color;
			yield return new WaitForSeconds (waitTime);
		}
		color.a = opacity;
		renderer.material.color = color;

		if (OnFadeInComplete != null) {
			OnFadeInComplete ();
		}
		isRunning = false;
	}

	IEnumerator DoFlicker(float time) {
		if (isRunning) {
			return false;
		}
		isRunning = true;
		time = 0.12f;
		float totalTime = 0.0f;
		float waitTime;
		Color originalColor = renderer.material.color;
		while (totalTime < time) {
			waitTime = Random.Range(0.0f, 0.09f);
			color.a = Random.Range (0.2f, 0.4f);
			renderer.material.color = color;
			totalTime += waitTime;
			yield return new WaitForSeconds(waitTime);
		}
		renderer.material.color = originalColor;
		isRunning = false;
	}
	
}
