<template>
	<div id="layout">
		<Header/>
		<div id="main">
			<nuxt/>
			<div id="side">
				<side-suggest/>
			</div>
		</div>
		<Footer/>
	</div>
</template>
<script>
import Header from '@/components/home/Header';
import Footer from '@/components/home/Footer';
import SideSuggest from '@/components/side-suggest';

export default {
	components: {
		Header,
		Footer,
		SideSuggest
	}
};
</script>

<style lang="less" scope>

@import "~assets/css/common.less";
@import "~assets/css/common_avairail.less";
@import "//at.alicdn.com/t/font_1223712_6mdctqou01y.css";
#layout{
  background: #F9FBFC;
  min-width: 1162px;

}
#main {
  width: 1162px;
  min-height: 400px;
  margin: 0 auto;
  position: relative;
  #side {
    position: absolute;
    top: 0;
    right: -52px;
    width: 52px;
  }
  // .side {
  //   position: -webkit-sticky;
  //   position: sticky;
  //   // top: 10px;
  //   bottom: 60vh;
  //   left: 100%;
  //   margin-right: -55px;
  //   width: 52px;
  // }
  //   #side {
  //   position: fixed;
  //   right: 200px;
  //   top:520px;
  //   bottom: 50vh;
  //   right: 0;
  //   width: 52px;
  // }
}
</style>
