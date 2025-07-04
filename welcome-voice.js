// welcome-voice.js

window.addEventListener("DOMContentLoaded", () => {
  if (!sessionStorage.getItem("welcomeVoicePlayed")) {
    // Use the username from global JS variable - Fixed: Added backticks for template literal
    const welcomeMessage = `Welcome to JS Weby website, ${username}`;
    const utterance = new SpeechSynthesisUtterance(welcomeMessage);

    // Optional voice settings
    utterance.pitch = 1;
    utterance.rate = 1;
    utterance.volume = 1;

    speechSynthesis.speak(utterance);

    sessionStorage.setItem("welcomeVoicePlayed", "true");
  }
});
