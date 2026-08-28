<template>
	<div id="layout">
		<Header/>
		<main>
			<nuxt/>
		</main>
		<Footer/>
	</div>
</template>
<script>
import Header from '@/components/home/Header.vue';
import Footer from '@/components/home/Footer.vue';
export default {
	components: {
		Header,
		Footer,
	}
};
</script>

<style lang="less" scope>
@import "../assets/css/common.less";
@import "../assets/css/common_avairail.less";
@import "//at.alicdn.com/t/font_1223712_6mdctqou01y.css";
#layout{
  background: #F9FBFC;
  min-width: 1162px;
}
</style>
