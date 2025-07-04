<?php
$total_cart_items = 0;
if(isset($_SESSION['mycart']))
{
    $total_cart_items = count($_SESSION['mycart']);
}
?>

<div class="header-main tophead-specific">
    <script src="../google-translate-widget.js"></script>
    <div class="container">
        <!-- logo section -->
        <a href="./index.php?id=<?php echo (isset($_SESSION['customer_name'])) ? $_SESSION['id'] : 'unknown'; ?>"
            class="header-logo" style="color: hsl(0, 0%, 13%);">

        </a>

        <!-- search input -->
        <div class="header-search-container">
            <form class="search-form" method="post" action="./search.php">

                <button type="button" id="voice-btn" class="voice-btn">
                    <ion-icon name="mic-outline"></ion-icon>
                </button>

                <input type="search" name="search" id="search-field" class="search-field"
                    placeholder="Enter your product name..." required
                    oninvalid="this.setCustomValidity('Enter product name...')" oninput="this.setCustomValidity('')" />

                <button type="submit" name="submit" class="search-btn">
                    <ion-icon name="search-outline"></ion-icon>
                </button>
            </form>
        </div>

        <div class="header-user-actions">

            <!-- Logout button -->
            <?php if(isset($_SESSION['id'])) { ?>

            <button type="button" id="lg-btn" class="action-btn">
                <a href="logout.php" id="a" role="button">
                    <ion-icon name="log-out-outline"></ion-icon>
                </a>
            </button>

            <!-- TODO: This script does not execute: Work on this, Directly logout user -->
            <script src="./js/logout.js"></script>

            <?php } else { ?>

            <?php } ?>

            <!-- Favourite Counter -->
            <button type="button" class="action-btn">
                <ion-icon name="heart-outline"></ion-icon>
                <span class="count">0</span>
            </button>

            <!-- Cart Button -->
            <?php if(!(isset($_SESSION['logged-in']))) { ?>

            <button type="button" class="action-btn">
                <a href="./cart.php">
                    <ion-icon name="bag-handle-outline"></ion-icon>
                </a>
                <span class="count">
                    <?php echo $total_cart_items; ?>
                </span>
            </button>

            <?php } ?>

        </div>
    </div>
</div>

<style>
/* All styles are now scoped to .tophead-specific to prevent conflicts */

/* Search form container */
.tophead-specific .header-search-container {
    flex: 1;
    max-width: 500px;
    margin: 0 20px;
}

/* Search form styling with proper alignment */
.tophead-specific .search-form {
    display: flex;
    align-items: stretch;
    width: 100%;
    border: 2px solid #e7e7e7;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    height: 50px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease;
}

.tophead-specific .search-form:focus-within {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
}

/* Voice button styling - positioned first */
.tophead-specific .voice-btn {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    border-right: 2px solid #e7e7e7;
    padding: 0 16px;
    cursor: pointer;
    color: #666;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    min-width: 60px;
    height: 100%;
    position: relative;
}

.tophead-specific .voice-btn:hover {
    color: #333;
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    transform: translateY(-1px);
}

.tophead-specific .voice-btn:active {
    transform: translateY(0);
}

.tophead-specific .voice-btn.listening {
    color: #fff;
    background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
    animation: tophead-pulse 1.5s infinite;
    box-shadow: 0 0 20px rgba(255, 107, 107, 0.5);
}

@keyframes tophead-pulse {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.05);
    }

    100% {
        transform: scale(1);
    }
}

/* Search input field - positioned in middle */
.tophead-specific .search-field {
    flex: 1;
    padding: 0 20px;
    border: none;
    outline: none;
    font-size: 16px;
    background: transparent;
    min-width: 0;
    height: 100%;
    color: #333;
}

.tophead-specific .search-field::placeholder {
    color: #999;
    font-style: italic;
}

.tophead-specific .search-field:focus {
    outline: none;
    background-color: #fafafa;
}

/* Search button styling - positioned last */
.tophead-specific .search-btn {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    border-left: 2px solid #e7e7e7;
    padding: 0 20px;
    cursor: pointer;
    color: #fff;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    min-width: 70px;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.tophead-specific .search-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.tophead-specific .search-btn:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.tophead-specific .search-btn:hover::before {
    left: 100%;
}

.tophead-specific .search-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.2);
}

/* Container layout */
.tophead-specific .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 15px 20px;
}

/* Header user actions */
.tophead-specific .header-user-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* Action buttons */
.tophead-specific .action-btn {
    background: none;
    border: none;
    padding: 10px;
    cursor: pointer;
    color: #666;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.3s ease;
    border-radius: 6px;
    min-width: 44px;
    height: 44px;
}

.tophead-specific .action-btn:hover {
    color: #333;
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.tophead-specific .action-btn .count {
    position: absolute;
    top: -2px;
    right: -2px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 11px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Logo styling */
.tophead-specific .header-logo {
    text-decoration: none;
    min-width: 120px;
    transition: transform 0.3s ease;
}

.tophead-specific .header-logo:hover {
    transform: scale(1.05);
}

.tophead-specific .logo-text-modern {
    text-decoration: none;
    color: inherit;
    font-weight: bold;
    font-size: 24px;
}

/* Responsive design */
@media (max-width: 768px) {
    .tophead-specific .container {
        gap: 10px;
        padding: 12px 15px;
    }

    .tophead-specific .header-search-container {
        margin: 0 10px;
        max-width: none;
        flex: 1;
    }

    .tophead-specific .search-form {
        height: 46px;
        border-width: 1px;
    }

    .tophead-specific .voice-btn {
        min-width: 50px;
        font-size: 18px;
        padding: 0 12px;
    }

    .tophead-specific .search-btn {
        min-width: 60px;

        font-size: 18px;
        padding: 0 16px;
    }

    .tophead-specific .search-field {
        padding: 0 15px;
        font-size: 15px;
    }

    .tophead-specific .header-user-actions {
        gap: 10px;
    }

    .tophead-specific .action-btn {
        min-width: 40px;
        height: 40px;
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .tophead-specific .container {
        gap: 8px;
        padding: 10px 12px;
    }

    .tophead-specific .header-search-container {
        margin: 0 5px;
    }

    .tophead-specific .search-form {
        height: 42px;
    }

    .tophead-specific .voice-btn {
        min-width: 45px;
        font-size: 16px;
        padding: 0 10px;
    }

    .tophead-specific .search-btn {
        min-width: 55px;
        font-size: 16px;
        padding: 0 14px;
    }

    .tophead-specific .search-field {
        padding: 0 12px;
        font-size: 14px;
    }

    .tophead-specific .action-btn {
        min-width: 36px;
        height: 36px;
        font-size: 16px;
    }

    .tophead-specific .logo-text-modern {
        font-size: 20px;
    }
}
</style>

<script>
// JavaScript remains the same as it uses specific IDs
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing speech recognition');

    // Get the voice button and search field elements
    const voiceBtn = document.getElementById('voice-btn');
    const searchField = document.getElementById('search-field');
    const searchBtn = document.querySelector('.tophead-specific .search-btn'); // Made more specific

    // Debug check to ensure elements are found
    if (!voiceBtn) {
        console.error('Voice button not found!');
        return;
    }

    if (!searchField) {
        console.error('Search field not found!');
        return;
    }

    if (!searchBtn) {
        console.error('Search button not found!');
        return;
    }

    console.log('All elements found successfully');

    // Add search button click handler
    searchBtn.addEventListener('click', function(e) {
        const searchValue = searchField.value.trim();
        if (!searchValue) {
            e.preventDefault();
            searchField.focus();
            searchField.setCustomValidity('Please enter a search term');
            searchField.reportValidity();
            return false;
        }
        console.log('Search submitted with value:', searchValue);
    });

    // Add enter key handler for search field
    searchField.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchValue = this.value.trim();
            if (!searchValue) {
                e.preventDefault();
                this.setCustomValidity('Please enter a search term');
                this.reportValidity();
                return false;
            }
        }
    });

    // Clear custom validity on input
    searchField.addEventListener('input', function() {
        this.setCustomValidity('');
    });

    // Check if browser supports speech recognition
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        console.log('Speech recognition is supported');

        // Create speech recognition instance
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new SpeechRecognition();

        // Configure recognition
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';
        recognition.maxAlternatives = 1;

        // Add click event listener to voice button
        voiceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Voice button clicked');

            try {
                // Start recognition
                recognition.start();
                console.log('Speech recognition started');

                // Visual feedback
                voiceBtn.classList.add('listening');
                const voiceIcon = voiceBtn.querySelector('ion-icon');
                if (voiceIcon) {
                    voiceIcon.setAttribute('name', 'radio-button-on');
                }
            } catch (error) {
                console.error('Error starting speech recognition:', error);
                alert('Error starting speech recognition. Please try again.');
            }
        });

        // Handle recognition results
        recognition.onresult = function(event) {
            console.log('Got speech recognition result');
            if (event.results && event.results.length > 0) {
                const transcript = event.results[0][0].transcript;
                console.log('Transcript:', transcript);
                searchField.value = transcript;
                searchField.focus();

                // Optional: Auto-submit after speech recognition
                // setTimeout(() => {
                //     if (transcript.trim()) {
                //         searchBtn.click();
                //     }
                // }, 1000);
            }
        };

        // Handle recognition end
        recognition.onend = function() {
            console.log('Speech recognition ended');
            voiceBtn.classList.remove('listening');
            const voiceIcon = voiceBtn.querySelector('ion-icon');
            if (voiceIcon) {
                voiceIcon.setAttribute('name', 'mic-outline');
            }
        };

        // Handle recognition errors
        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            voiceBtn.classList.remove('listening');
            const voiceIcon = voiceBtn.querySelector('ion-icon');
            if (voiceIcon) {
                voiceIcon.setAttribute('name', 'mic-outline');
            }

            // Show user-friendly error message
            let errorMessage = 'Speech recognition error: ';
            switch (event.error) {
                case 'no-speech':
                    errorMessage += 'No speech detected. Please try again.';
                    break;
                case 'audio-capture':
                    errorMessage += 'Microphone not accessible.';
                    break;
                case 'not-allowed':
                    errorMessage += 'Microphone permission denied.';
                    break;
                case 'network':
                    errorMessage += 'Network error occurred.';
                    break;
                default:
                    errorMessage += 'Please try again.';
            }
            console.warn(errorMessage);
        };

        // Handle recognition start
        recognition.onstart = function() {
            console.log('Speech recognition started successfully');
        };

    } else {
        console.warn('Speech recognition not supported in this browser');
        // Hide the voice button if speech recognition is not supported
        voiceBtn.style.display = 'none';
    }
});
</script>