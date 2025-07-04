async function translateText(text, targetLang = "ta") {
  const response = await fetch("https://libretranslate.com/translate", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      q: text,
      source: "en",
      target: targetLang,
      format: "text",
    }),
  });
  const data = await response.json();
  return data.translatedText;
}

// Translate all elements with class "translate"
async function translatePage(targetLang) {
  const elements = document.querySelectorAll(".translate");
  for (let el of elements) {
    const originalText = el.getAttribute("data-original") || el.textContent;
    el.setAttribute("data-original", originalText); // save original for switching back
    const translated = await translateText(originalText, targetLang);
    el.textContent = translated;
  }
}

// Language dropdown change event
document.getElementById("langSelect").addEventListener("change", (e) => {
  const lang = e.target.value;
  translatePage(lang);
});
