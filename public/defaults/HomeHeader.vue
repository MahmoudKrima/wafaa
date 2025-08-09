<template>
  <div class="header-background min-h-[700px] pb-[40px]">
    <div class="container">
      <div class="flex items-center justify-center md:flex-row flex-col">
        <div class="mt-[50px] md:mt-[140px] md:mb-[50px] px-[15px] md:order-0 order-1">
          <div>
            <home-form />
          </div>
        </div>
        <div class="mt-[80px] md:mt-[140px] md:mb-[50px] px-[15px] md:order-1 order-0">
          <div>
            <img
              class="hero-img md:w-[100%] sm:w-[290px] object-fit"
              :src="currentImage"
              alt="startSlideshow"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import HomeForm from './HomeForm.vue'

import { ref, onMounted, onBeforeUnmount } from 'vue'
import hero from '../../icons/hero.png'
import designImg from '../../icons/design-experience-home-img.png'
import webDevImg from '../../icons/web-development-home-img.png'
const images = [hero, designImg, webDevImg]
const currentImage = ref(images[0])
let index = 0
let intervalId = null

function startSlideshow() {
  intervalId = setInterval(() => {
    index = (index + 1) % images.length
    currentImage.value = images[index]
  }, 5000)
}
onMounted(() => {
  startSlideshow()
})

onBeforeUnmount(() => {
  clearInterval(intervalId)
})
</script>
<style scoped>
.hero-img {
  height: 437px;
  width: 515px;
  object-fit: fill;
}

@media (max-width: 639px) {
  .hero-img {
    max-height: 250px;
    width: 290px;
  }
}
</style>
