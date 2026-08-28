<template>
	<div class="filter-item">
		<span class="label">
			{{label}}
		</span>
		<div class="option">
			<slot></slot>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		label: String
	}
};
</script>

<style lang="less" scoped>
.filter-item {
  display: flex;
  align-items: center;
  min-height: 50px;
  padding: 8px 16px;
  .label {
    padding: 0 20px;
    color: #CBCBCB;
    font-size: 16px;
    font-weight: 400;
    flex-shrink: 0;
  }
  .option {
    flex: 1;
    flex-wrap: wrap;
    padding: 0 8px;
  }
}
</style>
