{{-- Image Editor Modal — self-contained styling, does not depend on Tailwind JIT --}}
<div
    x-data="imageEditor()"
    x-show="open"
    x-transition.opacity
    @keydown.escape.window="close()"
    @open-image-editor.window="openEditor($event.detail.url, $event.detail.path)"
    class="amer-editor"
    id="amer-image-editor"
    style="display:none;"
>
    {{-- ── TOP BAR ─────────────────────────────────────────────────────── --}}
    <div class="amer-editor__topbar">
        <div class="amer-editor__brand">
            <div class="amer-editor__logo">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span>محرر الصور</span>
        </div>

        <div class="amer-editor__tabs">
            <button type="button" @click="mode='crop'"    :class="mode==='crop'    ? 'is-active' : ''">قص وتدوير</button>
            <button type="button" @click="mode='adjust'"  :class="mode==='adjust'  ? 'is-active' : ''">ضبط</button>
            <button type="button" @click="mode='filters'" :class="mode==='filters' ? 'is-active' : ''">فلاتر</button>
        </div>

        <button type="button" @click="saveImage()" :disabled="loading"
                class="amer-editor__save" :class="saved ? 'is-saved' : ''">
            <span x-text="saved ? 'تم الحفظ ✓' : (loading ? 'جارٍ الحفظ…' : '💾 حفظ')"></span>
        </button>

        <button type="button" @click="close()" class="amer-editor__close" title="إغلاق">✕</button>
    </div>

    {{-- ── MAIN AREA ───────────────────────────────────────────────────── --}}
    <div class="amer-editor__main">

        {{-- Canvas --}}
        <div class="amer-editor__canvas">
            <img x-ref="cropImg" :src="imageUrl" alt="edit">
        </div>

        {{-- Side panel --}}
        <div class="amer-editor__panel">

            {{-- CROP --}}
            <div x-show="mode==='crop'" class="amer-editor__section">
                <p class="amer-editor__label">أدوات التحرير</p>
                <div class="amer-editor__grid">
                    <button type="button" @click="rotateLeft()"  class="amer-editor__btn" title="تدوير يسار">↶</button>
                    <button type="button" @click="rotateRight()" class="amer-editor__btn" title="تدوير يمين">↷</button>
                    <button type="button" @click="resetCrop()"   class="amer-editor__btn" title="إعادة تعيين">⟳</button>
                    <button type="button" @click="flipH()"       class="amer-editor__btn" title="قلب أفقي">⇋</button>
                    <button type="button" @click="flipV()"       class="amer-editor__btn" title="قلب عمودي">⇅</button>
                    <button type="button" @click="zoomIn()"      class="amer-editor__btn" title="تكبير">＋</button>
                </div>

                <p class="amer-editor__label" style="margin-top:14px;">نسبة العرض إلى الارتفاع</p>
                <div class="amer-editor__ratios">
                    <template x-for="(asp, key) in aspects" :key="key">
                        <button type="button" @click="setAspect(key)"
                                :class="selectedAspect === key ? 'is-active' : ''"
                                class="amer-editor__ratio"
                                x-text="asp.label"></button>
                    </template>
                </div>

                <p class="amer-editor__label" style="margin-top:14px;">التكبير</p>
                <div class="amer-editor__zoom">
                    <button type="button" @click="zoomOut()" class="amer-editor__icon-btn">−</button>
                    <input type="range" min="-5" max="5" step="0.5" value="0"
                           @input="cropper && cropper.zoom(parseFloat($event.target.value) * 0.05)"
                           class="amer-editor__range">
                    <button type="button" @click="zoomIn()" class="amer-editor__icon-btn">＋</button>
                </div>
            </div>

            {{-- ADJUST --}}
            <div x-show="mode==='adjust'" class="amer-editor__section">
                <p class="amer-editor__label">ضبط الصورة</p>

                <div class="amer-editor__slider">
                    <div class="amer-editor__slider-head">
                        <span>☀️ السطوع</span>
                        <span class="amer-editor__num" x-text="brightness + '%'"></span>
                    </div>
                    <input type="range" min="0" max="200" step="1" x-model.number="brightness"
                           @input="activeFilter='none'; applyVisualFilter()" class="amer-editor__range">
                </div>

                <div class="amer-editor__slider">
                    <div class="amer-editor__slider-head">
                        <span>◑ التباين</span>
                        <span class="amer-editor__num" x-text="contrast + '%'"></span>
                    </div>
                    <input type="range" min="0" max="200" step="1" x-model.number="contrast"
                           @input="activeFilter='none'; applyVisualFilter()" class="amer-editor__range">
                </div>

                <div class="amer-editor__slider">
                    <div class="amer-editor__slider-head">
                        <span>🎨 الإشباع</span>
                        <span class="amer-editor__num" x-text="saturation + '%'"></span>
                    </div>
                    <input type="range" min="0" max="300" step="1" x-model.number="saturation"
                           @input="activeFilter='none'; applyVisualFilter()" class="amer-editor__range">
                </div>

                <button type="button"
                        @click="brightness=100; contrast=100; saturation=100; applyVisualFilter()"
                        class="amer-editor__reset">إعادة تعيين الضبط</button>
            </div>

            {{-- FILTERS --}}
            <div x-show="mode==='filters'" class="amer-editor__section">
                <p class="amer-editor__label">فلاتر جاهزة</p>
                <div class="amer-editor__filters">
                    <template x-for="(f, key) in filters" :key="key">
                        <button type="button" @click="setFilter(key)"
                                class="amer-editor__filter"
                                :class="activeFilter === key ? 'is-active' : ''">
                            <div class="amer-editor__filter-img">
                                <img :src="imageUrl" :style="'filter:' + f.css">
                            </div>
                            <span x-text="f.label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom status --}}
    <div class="amer-editor__status">
        <span>اسحب زوايا منطقة القص لتعديلها</span>
        <span style="margin:0 .5rem;">•</span>
        <span>نقرة مزدوجة للتبديل بين القص والتحريك</span>
        <span style="margin-inline-start:auto;">الصورة تُحفظ WebP — جودة 88٪</span>
    </div>
</div>

<style>
/* ============================================================
   AMER IMAGE EDITOR — self-contained styles
   ============================================================ */
.amer-editor {
    position: fixed; inset: 0; z-index: 9999;
    display: flex; flex-direction: column;
    background: rgba(8, 12, 20, .97);
    font-family: 'Tajawal', system-ui, sans-serif;
    color: rgba(255,255,255,.85);
}

/* ── Top bar ────────────────────────────────────────────────── */
.amer-editor__topbar {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 18px;
    background: #111827;
    border-bottom: 1px solid rgba(255,255,255,.08);
    flex-shrink: 0;
}
.amer-editor__brand { display:flex; align-items:center; gap:8px; margin-inline-end:auto; }
.amer-editor__brand span { color:#fff; font-weight:700; font-size:14px; }
.amer-editor__logo {
    width:28px; height:28px; border-radius:8px;
    background:#C9A84C; display:flex; align-items:center; justify-content:center; color:#fff;
}

.amer-editor__tabs {
    display:flex; gap:4px; background:rgba(255,255,255,.05);
    padding:4px; border-radius:10px;
}
.amer-editor__tabs button {
    padding:6px 14px; border:0; background:transparent;
    color:rgba(255,255,255,.55); font-size:12px; font-weight:600;
    border-radius:6px; cursor:pointer; transition: all .15s;
}
.amer-editor__tabs button:hover { color:#fff; }
.amer-editor__tabs button.is-active { background:rgba(255,255,255,.15); color:#fff; }

.amer-editor__save {
    padding:8px 18px; border:0; border-radius:8px;
    background:#C9A84C; color:#fff;
    font-weight:700; font-size:13px; cursor:pointer;
    transition:all .2s;
}
.amer-editor__save:hover:not(:disabled) { background:#d4b35a; transform:translateY(-1px); }
.amer-editor__save:disabled { opacity:.6; cursor:wait; }
.amer-editor__save.is-saved { background:#10b981; }

.amer-editor__close {
    width:32px; height:32px; border:0; border-radius:8px;
    background:transparent; color:rgba(255,255,255,.5);
    font-size:16px; cursor:pointer; transition:all .15s;
}
.amer-editor__close:hover { background:rgba(255,255,255,.08); color:#fff; }

/* ── Main ──────────────────────────────────────────────────── */
.amer-editor__main { flex:1; display:flex; min-height:0; }

.amer-editor__canvas {
    flex:1; min-width:0; overflow:hidden;
    display:flex; align-items:center; justify-content:center;
    padding:16px; background:#0d1117;
}
.amer-editor__canvas img { max-width:100%; max-height:100%; display:block; }

.amer-editor__panel {
    width:280px; flex-shrink:0;
    overflow-y:auto;
    background:#111827;
    border-inline-start:1px solid rgba(255,255,255,.08);
    padding:18px;
}

.amer-editor__section { display:flex; flex-direction:column; gap:6px; }

.amer-editor__label {
    color:rgba(255,255,255,.4);
    font-size:11px; font-weight:600;
    text-transform:uppercase; letter-spacing:.08em;
    margin:0 0 6px 0;
}

/* ── Tool grid (3 columns) ────────────────────────────────── */
.amer-editor__grid {
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:6px;
}
.amer-editor__btn {
    display:flex; align-items:center; justify-content:center;
    height:38px;
    background:rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.08);
    border-radius:8px;
    color:rgba(255,255,255,.75);
    font-size:18px; cursor:pointer; transition:all .15s;
}
.amer-editor__btn:hover {
    background:rgba(201,168,76,.2);
    color:#C9A84C;
    border-color:rgba(201,168,76,.4);
}

/* ── Aspect ratios ────────────────────────────────────────── */
.amer-editor__ratios { display:flex; flex-wrap:wrap; gap:5px; }
.amer-editor__ratio {
    padding:5px 10px;
    background:rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.08);
    border-radius:6px;
    color:rgba(255,255,255,.65);
    font-size:11px; font-weight:600;
    cursor:pointer; transition:all .15s;
}
.amer-editor__ratio:hover { background:rgba(255,255,255,.12); color:#fff; }
.amer-editor__ratio.is-active {
    background:#C9A84C; color:#fff; border-color:#C9A84C;
}

/* ── Zoom row ─────────────────────────────────────────────── */
.amer-editor__zoom { display:flex; align-items:center; gap:8px; }
.amer-editor__icon-btn {
    width:28px; height:28px;
    background:rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.08);
    border-radius:6px;
    color:rgba(255,255,255,.7);
    cursor:pointer;
    font-size:16px; line-height:1;
}
.amer-editor__icon-btn:hover { background:rgba(255,255,255,.12); color:#fff; }

/* ── Sliders (adjust) ─────────────────────────────────────── */
.amer-editor__slider { margin-bottom:14px; }
.amer-editor__slider-head {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:6px;
    color:rgba(255,255,255,.7); font-size:12px;
}
.amer-editor__num { color:rgba(255,255,255,.45); font-variant-numeric:tabular-nums; }

.amer-editor__range {
    -webkit-appearance:none; appearance:none;
    width:100%; height:4px; border-radius:999px;
    background:rgba(255,255,255,.12); outline:none; cursor:pointer;
}
.amer-editor__range::-webkit-slider-thumb {
    -webkit-appearance:none;
    width:14px; height:14px; border-radius:50%;
    background:#C9A84C; cursor:pointer;
    border:2px solid rgba(255,255,255,.3);
    transition:transform .1s;
}
.amer-editor__range::-webkit-slider-thumb:hover { transform:scale(1.2); }
.amer-editor__range::-moz-range-thumb {
    width:14px; height:14px; border-radius:50%;
    background:#C9A84C; cursor:pointer; border:2px solid rgba(255,255,255,.3);
}

.amer-editor__reset {
    margin-top:8px;
    background:transparent; border:0;
    color:rgba(255,255,255,.45);
    font-size:11px; text-decoration:underline; cursor:pointer;
}
.amer-editor__reset:hover { color:#fff; }

/* ── Filters grid ─────────────────────────────────────────── */
.amer-editor__filters {
    display:grid;
    grid-template-columns: repeat(2, 1fr);
    gap:8px;
}
.amer-editor__filter {
    display:flex; flex-direction:column; align-items:center; gap:6px;
    padding:8px;
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.1);
    border-radius:10px;
    cursor:pointer; transition:all .15s;
}
.amer-editor__filter:hover { border-color:rgba(255,255,255,.25); }
.amer-editor__filter.is-active {
    border-color:#C9A84C;
    background:rgba(201,168,76,.1);
}
.amer-editor__filter-img {
    width:100%; height:60px;
    border-radius:6px; overflow:hidden;
    background:#0d1117;
}
.amer-editor__filter-img img {
    width:100%; height:100%; object-fit:cover; display:block;
}
.amer-editor__filter span {
    color:rgba(255,255,255,.7); font-size:11px; font-weight:600;
}

/* ── Status ──────────────────────────────────────────────── */
.amer-editor__status {
    display:flex; align-items:center;
    padding:8px 18px;
    background:#0d1117;
    border-top:1px solid rgba(255,255,255,.06);
    color:rgba(255,255,255,.35); font-size:11px;
    flex-shrink:0;
}

/* ── Cropper.js dark theme overrides ──────────────────────── */
.cropper-bg     { background-image: repeating-conic-gradient(#1f2937 0% 25%, #111827 0% 50%) !important; }
.cropper-view-box { outline-color:#C9A84C !important; outline-width:2px !important; }
.cropper-point  { background:#C9A84C !important; }
.cropper-line   { background: rgba(201,168,76,.5) !important; }
.cropper-dashed { border-color: rgba(201,168,76,.4) !important; }
.cropper-center::before, .cropper-center::after { background:#C9A84C !important; }
</style>
