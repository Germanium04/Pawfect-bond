<div class="forfun-container">
    <img src="{{asset('assets/move1.png')}}" class="dog frame1">
    <img src="{{asset('assets/move2.png')}}" class="dog frame2">
</div>

<style>
.forfun-container {
    position: fixed;   /* 👈 key change */
    bottom: 0;         /* stick to bottom */
    left: 0;
    width: 100%;
    height: 60px;     /* adjust based on dog size */
    pointer-events: none; /* optional: lets clicks pass through */
    overflow: hidden;
    z-index: 9999;     /* stays on top */
}

/* shared dog style */
.dog {
    position: absolute;
    width: 50px;
    height: auto;
    object-fit: contain;
}

/* frame animations */
.frame1 {
    animation: runAcross 8s linear infinite, run1 0.5s infinite;
}

.frame2 {
    animation: runAcross 8s linear infinite, run2 0.5s infinite;
}

/* running frames (slightly slower) */
@keyframes run1 {
    0%, 50%, 100% { opacity: 1; }
    25%, 75% { opacity: 0; }
}

@keyframes run2 {
    0%, 50%, 100% { opacity: 0; }
    25%, 75% { opacity: 1; }
}

/* movement: start OFF SCREEN LEFT → go RIGHT */
@keyframes runAcross {
    0% {
        left: 100%;      /* start off-screen RIGHT */
    }
    100% {
        left: -100px;    /* exit off-screen LEFT (dog width) */
    }
}
</style>