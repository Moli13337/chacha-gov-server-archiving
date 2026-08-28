<template>
	<div class="carousel-container">
		<div class="header">
			<span class="title">政策解读</span>
			<span class="more">
				更多解读
				<i class="el-icon-d-arrow-right"></i>
			</span>
		</div>
		<div class="body">
			<el-carousel
				height="220px"
				trigger="click"
				indicator-position="none"
				arrow="always"
				:interval="5000"
			>
				<el-carousel-item
					v-for="item in 4"
					:key="item"
					style="padding: 0 32px;"
				>
					<el-row :gutter="32">
						<el-col :span="8">
							<div class="item">
								<p class="title">专家详解软甲互联网税收优惠政策</p>
								<div class="description">多位专家和学者表示，这一政策对比前出台的政策延续，对产业税收优惠支持是合理合法的，复活条件的内容外资企业可以享受这一优惠政策</div>
								<p class="date">2019-05-30</p>
							</div>
						</el-col>
						<el-col :span="8">
							<div class="item">
								<p class="title">专家详解软甲互联网税收优惠政策</p>
								<div class="description">多位专家和学者表示，这一政策对比前出台的政策延续，对产业税收优惠支持是合理合法的，复活条件的内容外资企业可以享受这一优惠政策</div>
								<p class="date">2019-05-30</p>
							</div>
						</el-col>
						<el-col :span="8">
							<div class="item">
								<p class="title">专家详解软甲互联网税收优惠政策</p>
								<div class="description">多位专家和学者表示，这一政策对比前出台的政策延续，对产业税收优惠支持是合理合法的，复活条件的内容外资企业可以享受这一优惠政策</div>
								<p class="date">2019-05-30</p>
							</div>
						</el-col>
					</el-row>
				</el-carousel-item>
			</el-carousel>
		</div>
	</div>
</template>

<style lang="less" scoped>
@import "~assets/css/common_avairail.less";
.carousel-container {
  padding: 8px 16px;
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
  background: @backGroundColor;
  .header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    .title{
      font-size: 16px;
      font-family: Microsoft YaHei;
      font-weight: bold;
    }
    .more {
      font-size: 14px;
      font-family: Microsoft YaHei;
      font-weight: 400;
      color: @primaryColor;
    }
  }
  .body {
    padding: 16px 0;
    .item {
      height: 220px;
      border-radius: 5px;
      background-color: #F4F7F9;
      .title {
        font-size: 16px;
        font-family:Microsoft YaHei;
        font-weight:bold;
        padding: 20px 10px 20px 10px;
      }
      .description {
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        padding: 10px 10px 20px 10px;
        color: @textColor;
      }
      .date {
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: @borderLine;
        padding: 10px 10px;
      }
    }
  }
}
</style>

