<template>
	<div class="container">
		<Header/>
		<div class="resetSuccess-container">
			<p>密码重置成功</p>
			<p>下次请使用新密码进行登录</p>
			<el-button
				type="success"
				@click="handelToHome"
			>进入首页</el-button>
		</Div>
	</div>
</template>
<script>
import Header from '@/components/user/header';
export default {
	layout: 'empty',
	components: {
		Header,
	},
	methods: {
		handelToHome() {
			this.$router.push({name: 'index'});
		}
	}
};
</script>
<style lang="less" scoped>
.container {
.resetSuccess-container {
  width: 400px;
  height: 400px;
  margin: auto;
  background: red;
}
}

</style>

