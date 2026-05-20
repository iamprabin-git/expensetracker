let audioContext = null;

function getAudioContext() {
    if (!audioContext) {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) {
            return null;
        }
        audioContext = new AudioCtx();
    }

    return audioContext;
}

/**
 * Short bell-like chime for in-app notification alerts.
 */
export function playNotificationBell() {
    const ctx = getAudioContext();
    if (!ctx) {
        return;
    }

    const play = () => {
        const start = ctx.currentTime;
        const tones = [
            { freq: 880, delay: 0, duration: 0.45 },
            { freq: 1174.66, delay: 0.08, duration: 0.5 },
            { freq: 1318.51, delay: 0.16, duration: 0.55 },
        ];

        tones.forEach(({ freq, delay, duration }) => {
            const oscillator = ctx.createOscillator();
            const gain = ctx.createGain();

            oscillator.type = 'sine';
            oscillator.frequency.value = freq;

            gain.gain.setValueAtTime(0.0001, start + delay);
            gain.gain.exponentialRampToValueAtTime(0.22, start + delay + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, start + delay + duration);

            oscillator.connect(gain);
            gain.connect(ctx.destination);

            oscillator.start(start + delay);
            oscillator.stop(start + delay + duration + 0.05);
        });
    };

    if (ctx.state === 'suspended') {
        ctx.resume().then(play).catch(() => {});
        return;
    }

    play();
}

export function isNotificationSoundEnabled(root) {
    if (!root) {
        return true;
    }

    return root.dataset.notificationSound !== '0';
}

export function ringNotificationBell(root) {
    if (!isNotificationSoundEnabled(root)) {
        return;
    }

    playNotificationBell();

    const toggle = root.querySelector('[data-notifications-toggle]');
    if (toggle) {
        toggle.classList.remove('user-notifications__toggle--ring');
        // Force reflow so repeated alerts re-trigger animation
        void toggle.offsetWidth;
        toggle.classList.add('user-notifications__toggle--ring');
        window.setTimeout(() => toggle.classList.remove('user-notifications__toggle--ring'), 700);
    }
}
