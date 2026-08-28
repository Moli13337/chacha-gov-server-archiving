<template>
	<div class="qr-code-box">
		<div
			class="qr-code-btn"
			@click="handleBtnClick(false)"
		>
			<img
				class="switch-btn-icon"
				src="~assets/images/share/btn_left.png"
			/>
		</div>
		<div class="qr-code-container">
			<el-carousel
				ref="carousel"
				height="220px"
				trigger="click"
				indicator-position="outside"
				arrow="never"
				:interval="5000"
				:autoplay="false"
			>
				<el-carousel-item
					v-for="(pageItems, pageIndex) in qRCodeGrid"
					:key="pageIndex"
				>
					<el-row :gutter="32">
						<el-col
							:span="6"
							v-for="(item, itemIndex) in pageItems"
							:key="pageIndex * 4 + itemIndex"
						>
							<div class="qr-code-item">
								<img
									class="qr-code"
									:src="item.img"
								/>
								<p class="tip">
									[
									<span class="bold">关注公众号:</span>
									<span class="link">{{item.title}}</span>]
								</p>
							</div>
						</el-col>
					</el-row>
				</el-carousel-item>
			</el-carousel>
		</div>
		<div
			class="qr-code-btn"
			@click="handleBtnClick(true)"
		>
			<img
				class="switch-btn-icon"
				src="~assets/images/share/btn_right.png"
			/>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		qrCodeList: {
			type: Array,
			default() {
				return [];
			}
		},
		perCountRow: {
			type: Number,
			default: 4
		}
	},
	computed: {
		qRCodeGrid() {
			let qRCodeGrid = [];

			let pageCount = Math.ceil(this.qrCodeList.length / this.perCountRow);

			for (let pageIndex = 0; pageIndex < pageCount; pageIndex++) {
				let pageItems = [];

				for (let itemIndex = 0; itemIndex < this.perCountRow; itemIndex++) {
					let index = pageIndex * this.perCountRow + itemIndex;

					if (this.qrCodeList[index]) {
						pageItems.push(this.qrCodeList[index]);
					}
				}
				qRCodeGrid.push(pageItems);
			}

			return qRCodeGrid;
		}
	},
	methods: {
		handleBtnClick(isRight) {
			if (isRight) {
				this.$refs.carousel.next();
			} else {
				this.$refs.carousel.prev();
			}
		}
	}
};
</script>

<style lang="less">
.qr-code-box {
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  .qr-code-container {
    flex: 1;
    display: flex;
    justify-content: center;
    height: 220px;
    align-items: center;
    .el-carousel__container{
      height: 100%;
    }
    .qr-code-item {
      height: 220px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      flex-direction: column;
      .qr-code {
        width: 141px;
        height: 141px;
      }
    }
  }
  .qr-code-btn {
    width: 47px;
    height: 100%;
    cursor: pointer;
    .switch-btn-icon {
      width: 47px;
      height: 94px;
    }
  }
  .el-carousel {
    width: 100%;
  }
}
</style>
