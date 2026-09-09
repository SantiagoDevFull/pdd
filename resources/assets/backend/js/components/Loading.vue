<template>
    <div class="loading" v-if="show">
        <div class="loader-wrapper">
            <div class="loader-line"></div>
            <span class="loader-text">
                Cargando <span class="dots">...</span>
            </span>
        </div>
    </div>
</template>

<script>
import EventBus from '../event-bus';

export default {
    data() {
        return {
            show: false
        }
    },
    created() {
        EventBus.$on('loading', function (data) {
            this.show = data;
        }.bind(this));
    },
};
</script>

<style>
.loading {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    z-index: 9999;
}

.loader-wrapper {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.loader-line {
    position: relative;
    width: 300px;
    height: 8px;
    background: linear-gradient(to bottom,
            #bfc5cc,
            #f1f3f5);
    border-radius: 10px;
    overflow: hidden;
    box-shadow:
        inset 0 2px 3px rgba(0, 0, 0, 0.25),
        inset 0 -1px 2px rgba(255, 255, 255, 0.7),
        0 4px 10px rgba(0, 0, 0, 0.35);
}

.loader-line::before {
    content: '';
    position: absolute;
    height: 100%;
    width: 35%;
    background: linear-gradient(to bottom,
            #0fb3c8,
            #36d1dc,
            #0fb3c8);
    box-shadow:
        inset 0 1px 2px rgba(255, 255, 255, 0.8),
        0 2px 6px rgba(15, 179, 200, 0.8);
    animation: line-move 1.5s ease-in-out infinite;
    border-radius: 10px;
}

.loader-text {
    display: block;
    margin-top: 14px;
    font-size: 14px;
    color: #e5e7eb;
    font-weight: 500;
    letter-spacing: 0.4px;
}

.dots::after {
    content: '';
    animation: dots 1.4s infinite steps(4);
}

@keyframes line-move {
    0% {
        left: -35%;
    }

    100% {
        left: 100%;
    }
}

@keyframes dots {
    0% {
        content: '';
    }

    25% {
        content: '.';
    }

    50% {
        content: '..';
    }

    75% {
        content: '...';
    }

    100% {
        content: '';
    }
}
</style>
