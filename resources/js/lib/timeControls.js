export const DEFAULT_TIME_CONTROL = '10+5';

export const timeControlPresets = [
    {
        value: '3+2',
        label: '3+2 blitz',
        title: 'Blitz 3+2',
        minutes: 3,
        increment: 2,
    },
    {
        value: '5+0',
        label: '5+0 blitz',
        title: 'Blitz 5+0',
        minutes: 5,
        increment: 0,
    },
    {
        value: '10+5',
        label: '10+5 rapid',
        title: 'Rapid 10+5',
        minutes: 10,
        increment: 5,
    },
    {
        value: '15+10',
        label: '15+10 rapid',
        title: 'Rapid 15+10',
        minutes: 15,
        increment: 10,
    },
];

export const getTimeControlPreset = (value) => {
    const matched = timeControlPresets.find(
        (preset) => preset.value === value,
    );

    if (matched) {
        return matched;
    }

    const fallback = timeControlPresets.find(
        (preset) => preset.value === DEFAULT_TIME_CONTROL,
    );

    return fallback ?? timeControlPresets[0] ?? null;
};

export const parseTimeControl = (value) => {
    const preset = getTimeControlPreset(value);
    if (preset) {
        return {
            baseSeconds: preset.minutes * 60,
            incrementSeconds: preset.increment,
        };
    }

    if (typeof value === 'string') {
        const [basePart, incrementPart] = value.split('+');
        const baseMinutes = Number.parseInt(basePart, 10);
        const incrementSeconds = Number.parseInt(incrementPart, 10);

        if (Number.isFinite(baseMinutes) && Number.isFinite(incrementSeconds)) {
            return {
                baseSeconds: baseMinutes * 60,
                incrementSeconds,
            };
        }
    }

    return {
        baseSeconds: 10 * 60,
        incrementSeconds: 5,
    };
};

export const formatTimeControlLabel = (value) => {
    const preset = getTimeControlPreset(value);
    return preset ? preset.label : '10+5 rapid';
};

export const formatTimeControlTitle = (value) => {
    const preset = getTimeControlPreset(value);
    return preset ? preset.title : 'Rapid 10+5';
};

export const formatTimeControlDescription = (value) => {
    const preset = getTimeControlPreset(value);
    if (!preset) {
        return '10 minutes with 5 second increment.';
    }

    if (preset.increment <= 0) {
        return `${preset.minutes} minutes with no increment.`;
    }

    return `${preset.minutes} minutes with ${preset.increment} second${
        preset.increment === 1 ? '' : 's'
    } increment.`;
};

export const formatClockTime = (seconds) => {
    const safeSeconds = Math.max(0, Math.floor(Number(seconds) || 0));
    const minutes = Math.floor(safeSeconds / 60);
    const remainder = safeSeconds % 60;

    return `${String(minutes).padStart(2, '0')}:${String(remainder).padStart(
        2,
        '0',
    )}`;
};
