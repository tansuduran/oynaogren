let wordImageMap = [];  // JSON'dan gelen kelimeler buraya yüklenecek.

// JSON dosyasını kelimeler klasöründen yükle
fetch('kelimeler/kelimeler.json')
    .then(response => response.json())
    .then(data => {
        wordImageMap = data.words;  // Kelimeler JSON'dan yüklendi
        initializeGame();  // Kelimeler yüklendiğinde oyunu başlat
    })
    .catch(error => console.error('JSON dosyası yüklenirken hata oluştu:', error));

// ANETİL harf grubu
const anetilLetters = ['A', 'N', 'E', 'T', 'İ', 'L'];
let wrongAttempts = 0;  // Yanlış cevap sayacı
let correctAttempts = 0;  // Doğru cevap sayacı
let previousImageIndex = null;
let currentImageIndex = 0;

// Oyunu başlat düğmesine basıldığında ses çalmaya izin vereceğiz.
document.getElementById('start-button').addEventListener('click', () => {
    updateImage();
    document.getElementById('start-button').style.display = 'none'; // Düğmeyi gizle
    document.querySelector("#game-area img").style.display = 'block'; // Görseli göster
});

// Oyun başlatma fonksiyonu, JSON'dan kelimeler yüklendiğinde çağrılacak
function initializeGame() {
    document.getElementById('start-button').disabled = false;  // Başlat butonunu etkinleştir
    // Doğru ve yanlış sayacı sıfırlanıyor (görünüm olmasa da çalışıyor)
    wrongAttempts = 0;
    correctAttempts = 0;
}

// Ekrandaki resme bağlı olarak doğru kelimeyi bulalım
function getCorrectWord() {
    const currentImage = document.querySelector("#game-area img").src;
    const imageName = currentImage.split('/').pop();
    const wordObject = wordImageMap.find(item => item.gorsel === imageName);
    return wordObject ? wordObject.kelime : null;
}

// Resmi güncelleyen ve ses dosyasını 3 kez çalan fonksiyon
function updateImage() {
    wrongAttempts = 0;  // Her yeni soruda yanlış deneme sıfırlanır

    let newImageIndex;
    do {
        newImageIndex = Math.floor(Math.random() * wordImageMap.length);
    } while (newImageIndex === previousImageIndex);

    previousImageIndex = newImageIndex;
    currentImageIndex = newImageIndex;

    const imageElement = document.querySelector("#game-area img");
    imageElement.src = `gorseller/${wordImageMap[currentImageIndex].gorsel}`;

    playSoundMultipleTimes(`sounds/${wordImageMap[currentImageIndex].ses}`, 3, 2000);
    addImageClickEvent(`sounds/${wordImageMap[currentImageIndex].ses}`);
    
    const correctWord = getCorrectWord();
    createDropAreas(correctWord);  // Boş alanları oluştur
    createAnetilLetters();         // ANETİL harflerini göster
}

// 3 kez ses çalma fonksiyonu
function playSoundMultipleTimes(soundFile, repetitions, interval = 2000) {
    const audio = new Audio(soundFile);
    let count = 0;

    function playAudio() {
        if (count < repetitions) {
            audio.play().catch(error => {
                console.error("Ses oynatma hatası:", error);
            });
            count++;
            audio.addEventListener('ended', () => {
                setTimeout(() => {
                    if (count < repetitions) {
                        audio.currentTime = 0;
                        playAudio();
                    }
                }, interval);
            }, { once: true });
        }
    }

    playAudio();
}

// Görsele tıklanınca sesi bir kere daha oynatan fonksiyon
function addImageClickEvent(soundFile) {
    const imageElement = document.querySelector("#game-area img");
    imageElement.onclick = function() {
        const audio = new Audio(soundFile);
        audio.play();
    };
}

// Kelimenin uzunluğuna göre boş alanları dinamik olarak oluşturma
function createDropAreas(word) {
    const wordArea = document.getElementById('word-area');
    wordArea.innerHTML = '';  // Önceki boş alanları temizle

    for (let i = 0; i < word.length; i++) {
        const dropArea = document.createElement('span');
        dropArea.classList.add('drop-area');
        wordArea.appendChild(dropArea);
    }
}

// ANETİL harflerini gösterme
function createAnetilLetters() {
    const letterArea = document.getElementById('letter-area');
    letterArea.innerHTML = '';  // Önceki harfleri temizle

    anetilLetters.forEach((letter) => {
        const letterElement = document.createElement('span');
        letterElement.classList.add('letter');
        letterElement.textContent = letter;
        letterElement.style.cursor = 'pointer';
        letterArea.appendChild(letterElement);
    });

    enableTapToPlace();  // Harf yerleştirme işlevini yeniden aktif et
}

// Harflerin tıklandığında sırasıyla boş kutulara yerleşmesi
function enableTapToPlace() {
    document.querySelectorAll('.letter').forEach(letter => {
        letter.addEventListener('click', function() {
            const emptyArea = findEmptyDropArea();
            if (emptyArea) {
                const letterClone = letter.cloneNode(true);
                letterClone.classList.add('active');  // Seçilen harf aktif hale gelsin (renk değişimi)
                emptyArea.appendChild(letterClone);

                // Harf kutuya yerleştirildiğinde kutuyu gizle
                emptyArea.classList.add('has-letter');

                if (allDropAreasFilled()) {  // Yalnızca tüm boş alanlar dolduğunda kontrol et
                    checkWord();
                }
            }
        });
    });
}

// Boş kutu bulma fonksiyonu
function findEmptyDropArea() {
    return Array.from(document.querySelectorAll('.drop-area')).find(area => !area.hasChildNodes());
}

// Tüm boş alanlar dolu mu kontrol et
function allDropAreasFilled() {
    return Array.from(document.querySelectorAll('.drop-area')).every(area => area.hasChildNodes());
}

// Doğru kelimeyi kontrol eden fonksiyon
function checkWord() {
    const correctWord = getCorrectWord();
    if (!correctWord) {
        return;
    }

    let formedWord = "";
    document.querySelectorAll('.drop-area').forEach(area => {
        if (area.children.length > 0) {
            formedWord += area.children[0].textContent;
        }
    });

    if (formedWord === correctWord) {
        correctAttempts++;  // Doğru cevap sayısını artır
        playRandomCorrectSound();
        showMessage(true);
        setTimeout(() => {
            nextQuestion();
        }, 6000);
    } else {
        wrongAttempts++;  // Yanlış cevap sayısını artır

        if (wrongAttempts === 2) {
            playSound("sounds/result/bukelime.mp3");  // İkinci yanlış cevap sonrası doğru sesini çalar
            showMessage(false);
            setTimeout(() => {
                nextQuestion();
            }, 6000);
        } else {
            playRandomWrongSound();  // Yanlış cevapta rastgele ses çal
            setTimeout(() => {
                resetLetters();  // Harfleri sıfırla
                playSoundMultipleTimes(`sounds/${wordImageMap[currentImageIndex].ses}`, 3, 2000);  // Yanlış cevapta kelimenin sesi 3 kez, 2 saniye aralıkla çal
            }, 4000);
        }
    }
}

// Harfleri sıfırlayan fonksiyon
function resetLetters() {
    document.querySelectorAll('.drop-area').forEach(area => {
        area.innerHTML = '';
        area.classList.remove('has-letter');
    });

    createAnetilLetters();
}

// Sonraki soruya geçme fonksiyonu
function nextQuestion() {
    updateImage();
    const correctWord = getCorrectWord();
    createDropAreas(correctWord);
    createAnetilLetters();
}

// Ses dosyalarını oynatma
function playSound(soundFile) {
    const audio = new Audio(soundFile);
    audio.play();
}

// Mesaj kutusunu gösteren fonksiyon
function showMessage(isCorrect) {
    const messageBox = document.getElementById('message-box');
    const errorBox = document.getElementById('error-box');
    
    if (isCorrect) {
        messageBox.style.display = 'block';
        errorBox.style.display = 'none';
    } else {
        errorBox.style.display = 'block';
        messageBox.style.display = 'none';
    }

    setTimeout(() => {
        messageBox.style.display = 'none';
        errorBox.style.display = 'none';
    }, 3000);
}

// Rastgele doğru cevap sesi çalma fonksiyonu
function playRandomCorrectSound() {
    const correctSounds = ['sounds/result/alkis.mp3', 'sounds/result/harika.mp3'];
    const randomIndex = Math.floor(Math.random() * correctSounds.length);
    playSound(correctSounds[randomIndex]);
}

// Rastgele yanlış cevap sesi çalma fonksiyonu
function playRandomWrongSound() {
    const wrongSounds = ['sounds/result/no.mp3', 'sounds/result/yeniden.mp3'];
    const randomIndex = Math.floor(Math.random() * wrongSounds.length);
    playSound(wrongSounds[randomIndex]);
}
