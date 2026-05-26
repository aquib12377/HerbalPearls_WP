#!/usr/bin/env python3
"""
Convert all .ttf fonts in assets/fonts/ to subset Latin .woff2 files.

Subset is U+0000-024F + a few punctuation/symbol glyphs that the theme uses
in product copy: ₹ (rupee), ★ ☆ (stars), arrows, em-dash, en-dash, etc.
Drops Cyrillic/Greek/Vietnamese to shave ~70% off each file.

Run via: python3 tools/build-woff2.py
"""
import os
import sys
from fontTools.ttLib import TTFont
from fontTools.subset import Subsetter, Options

THEME_ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
FONT_DIR = os.path.join(THEME_ROOT, "assets", "fonts")

# Latin extended-A covers Western European accents (À, é, ñ, etc.)
UNICODE_RANGES = "U+0020-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+20B9,U+2122,U+2126,U+212E,U+2190-2199,U+2212,U+2215,U+FEFF,U+FFFD,U+2605-2606"

def convert(src_ttf: str) -> bool:
    name = os.path.basename(src_ttf).replace(".ttf", "")
    dst = os.path.join(FONT_DIR, f"{name}.woff2")

    font = TTFont(src_ttf)
    opts = Options()
    opts.flavor = "woff2"
    opts.with_zopfli = False  # brotli compression for woff2 is sufficient
    opts.desubroutinize = True
    opts.hinting = False  # tiny size win, modern browsers don't need TTF hinting
    opts.layout_features = ["kern", "liga", "clig"]
    opts.name_IDs = ["*"]
    opts.notdef_outline = True
    opts.recommended_glyphs = True
    opts.legacy_kern = False

    subsetter = Subsetter(options=opts)
    subsetter.populate(unicodes=parse_unicodes(UNICODE_RANGES))
    subsetter.subset(font)

    font.flavor = "woff2"
    font.save(dst)

    src_size = os.path.getsize(src_ttf)
    dst_size = os.path.getsize(dst)
    pct = (1 - dst_size / src_size) * 100
    print(f"  {name}.ttf  {src_size:>7} → {dst_size:>6} ({pct:.0f}% smaller)")
    return True


def parse_unicodes(spec: str):
    codes = set()
    for part in spec.split(","):
        part = part.strip().lstrip("U+")
        if "-" in part:
            a, b = part.split("-")
            codes.update(range(int(a, 16), int(b, 16) + 1))
        else:
            codes.add(int(part, 16))
    return codes


def main():
    if not os.path.isdir(FONT_DIR):
        print(f"Font dir not found: {FONT_DIR}", file=sys.stderr)
        sys.exit(1)

    ttfs = sorted(f for f in os.listdir(FONT_DIR) if f.endswith(".ttf"))
    if not ttfs:
        print("No .ttf files to convert.")
        return

    print(f"Converting {len(ttfs)} font(s) to subset WOFF2…")
    total_src = total_dst = 0
    for ttf in ttfs:
        src = os.path.join(FONT_DIR, ttf)
        total_src += os.path.getsize(src)
        convert(src)
        total_dst += os.path.getsize(os.path.join(FONT_DIR, ttf.replace(".ttf", ".woff2")))

    pct = (1 - total_dst / total_src) * 100
    print(f"\nTotal: {total_src:,} → {total_dst:,} bytes ({pct:.0f}% smaller)")


if __name__ == "__main__":
    main()
