import { defineConfig, presetUno } from 'unocss'
import presetRemToPx from '@unocss/preset-rem-to-px'
import { animatedUno } from 'animated-unocss'

export default defineConfig({
  presets: [
    presetUno(),
    animatedUno(),
    presetRemToPx({
      baseFontSize: 4
    })
  ],
  safelist: [
    ...['xl4', 'xl3', 'xl2', 'xl', 'lg', 'md', 'sm']
      .map((key) => {
        return Array.from({ length: 11 }, (_, i) => `${key}:grid-cols-${i + 1}`)
      })
      .flat(),
    ...Array.from({ length: 11 }, (_, i) => `grid-cols-${i + 1}`)
  ],
  shortcuts: {
    fc: 'flex items-center',
    fsb: 'flex justify-between',
    fcc: 'flex items-center justify-center',
    fcb: 'flex items-center justify-between',
    s0: 'shrink-0',
    pof: 'fixed',
    por: 'relative',
    pos: 'sticky',
    poa: 'absolute',
    tc: 'text-center'
  },
  theme: {
    breakpoints: {
      xl4: '2560px',
      xl3: '1920px',
      xl2: '1440px',
      xl: '1200px',
      lg: '992px',
      md: '768px',
      sm: '576px'
    }
  }
})
