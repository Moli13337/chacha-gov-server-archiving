<template>
	<FooterNote
		:title="title"
		:content="content"
	/>
</template>
<script>


import FooterNote from '~/components/footernote';
export default {
	components: {
		FooterNote
	},
	data() {
		return {
			title: '联系我们',
			content: `	<section>
						<h4>
						成都侠客岛企业管理有限公司：400-900-9088
						</h4>
					</section>`
		};
	},

};
</script>
<style lang="less" >

</style>

